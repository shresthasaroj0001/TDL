<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ItemCategory;
use App\Http\Controllers\Controller;
use DateTime;
use DB;
use Validator;

class CategoryController_unused extends Controller
{
    public function index()
    {
        $responses = DB::select("SELECT category_id, name, description FROM category WHERE is_deleted=0 ");
        // return $responses;
        return view('admin.category.index')->with('list', $responses);

        // if ($responses != null) {
        //     //return view('admin.category.index');//->with('menu_id', $id)->with('menu_title', $responses[0]->title);
        // }else{
        //     return "Hello";
        // }
    }


    public function create()
    {
        return view('admin.category.create');
    }

    /*
    public function indexx()
    {
        try{
            $response = DB::select('call getallcategories');
            return $response;
        }catch(\Exception $w){
            return array();
        }
       // $response = DB::select("SELECT categories.id,IFNULL(c.title,'') as imgId,categories.title,categories.showInfront,categories.stats,categories.orderb FROM categories LEFT JOIN ( SELECT blog_images.title,blog_images.blogCategory_id,blog_images.createdDate from blog_images GROUP BY blog_images.blogCategory_id HAVING MAX(blog_images.createdDate) ) c on categories.id=c.blogCategory_id where categories.isdeleted=0 order by categories.title asc;");

       // return $response;

        // try{
        //     $response = DB::select("SELECT blog_categories.id,blog_categories.title,blog_categories.showInfront,blog_categories.stats,blog_categories.orderb FROM blog_categories LEFT JOIN (SELECT blog_images.title,blog_images.blogCategory_id from blog_images GROUP BY blog_images.blogCategory_id HAVING MAX(blog_images.createdDate)) c on blog_categories.id=c.blogCategory_id where blog_categories.isdeleted=0");

        //     return $response;
        // }catch(\Exception $e){
        //     return array();
        // }
    }

    public function store(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'title' => 'required|max:240',
            'mfile' => 'mimes:jpeg,png,bmp,tiff |max:4096 ',
            'stats' => 'required|numeric',
            'orderb' => 'required|numeric',
            'showinfront' => 'required|numeric',
        ], $messages = [
            'mfile.required' => 'Please select image file.',
            'required' => 'The :attribute field is required.',
            'mimes' => 'Only jpeg, png, bmp,tiff are allowed.',
        ]
        );

        if ($Validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($Validator);
        }

        try {

            $fileNameToStore = "";
            if ($request->hasFile('mfile')) {
                // Get jst ext
                $extension = $request->file('mfile')->getClientOriginalExtension();
                //$filesize = $request->file('mfile')->getClientSize();

                // if ($filesize > 11534336) {
                //     return redirect()->back()->withInput($request->input())->with('error', 'Please select file less than 11MB');
                // }
                if (
                    (strcasecmp($extension, 'png') == 0) ||
                    (strcasecmp($extension, 'jpg') == 0) ||
                    (strcasecmp($extension, 'bmp') == 0) ||
                    (strcasecmp($extension, 'jpeg') == 0) ||
                    (strcasecmp($extension, 'gif') == 0)
                ) {
                    //Filename to store
                    $fileNameToStore = "" . time() . '.' . $extension;
                    //uplod image
                    $file = $request->file('mfile');
                    $destinationPath = public_path('/uploads/');
                    $file->move($destinationPath, $fileNameToStore);

                }
            }

            $blogCategorys = new Category();
            $blogCategorys->title = $request->title;
            $blogCategorys->showInfront = $request->showinfront;
            $blogCategorys->stats = $request->stats;
            $blogCategorys->orderb = $request->orderb;
            $blogCategorys->isdeleted = 0;

            DB::beginTransaction();

            $blogCategorys->save();

            if ($fileNameToStore != "") {
                DB::table('blog_images')->insert(['auth_id' => auth()->user()->id, 'blogCategory_id' => $blogCategorys->id, 'title' => $fileNameToStore, 'createdDate' => new DateTime()]);
            }
            DB::commit();
            return redirect()->route('blog-category.index')->with('success', "Category added successfully");
        } catch (\Exception $e) {
            return redirect()->route('blog-category.create')->with('error', "Failed. Please try again");

        }
    }

    public function getPicsForAjax($id)
    {
        if ($id > 0) {
            $response = DB::select("SELECT galleries.id,galleries.title,galleries.isfeatureimg,galleries.stats,galleries.orderb from galleries WHERE galleries.isdeleted=0 and galleries.menu_id=? ORDER BY galleries.id DESC", [$id]);
            return $response;
        }
        return array();
    }

    public function edit($id)
    {
        if ($id > 0) {
            $response = DB::select("SELECT id, title,showInfront, stats, orderb FROM categories where isdeleted=0 and id=?", [$id]);
            if ($response != null) {
                $blogCategorys = new Category();
                $blogCategorys->title = $response[0]->title;
                $blogCategorys->showInfront = $response[0]->showInfront;
                $blogCategorys->stats = $response[0]->stats;
                $blogCategorys->orderb = $response[0]->orderb;
                $blogCategorys->id = $id;

                return view('admin.category.edit')->with('blogCategorys', $blogCategorys);
            }
        }
        return redirect()->route('blog-category.index')->with('error', 'Category Not found');
    }

    public function update(Request $request, $id)
    {
        $Validator = Validator::make($request->all(), [
            'title' => 'required|max:240',
            'mmfile' => 'mimes:jpeg,png,bmp,tiff |max:4096 ',
            'stats' => 'required|numeric',
            'orderb' => 'required|numeric',
            'showinfront' => 'required|numeric',
        ], $messages = [
            'mmfile.required' => 'Please select image file.',
            'required' => 'The :attribute field is required.',
            'mimes' => 'Only jpeg, png, bmp,tiff are allowed.',
        ]
        );

        $response = DB::select("SELECT orderb FROM categories where isdeleted=0 and id=?", [$id]);
        if ($response == null) {
            return redirect()->route('blog-category.index')->with('error', 'Data not found. Error 2');
        }

        $blogCategorys = new Category();
        $blogCategorys->title = $request->title;
        $blogCategorys->showInfront = $request->showinfront;
        $blogCategorys->stats = $request->stats;
        $blogCategorys->orderb = $request->orderb;
        $blogCategorys->isdeleted = 0;
        $blogCategorys->id = $id;

        $fileNameToStore = "";
        //DB::beginTransaction();
        if ($request->hasFile('mmfile')) {
            // Get jst ext
            $extension = $request->file('mmfile')->getClientOriginalExtension();
            //$filesize = $request->file('mfile')->getClientSize();

            // if ($filesize > 11534336) {
            //     return redirect()->back()->withInput($request->input())->with('error', 'Please select file less than 11MB');
            // }
            if (
                (strcasecmp($extension, 'png') == 0) ||
                (strcasecmp($extension, 'jpg') == 0) ||
                (strcasecmp($extension, 'bmp') == 0) ||
                (strcasecmp($extension, 'jpeg') == 0) ||
                (strcasecmp($extension, 'gif') == 0)
            ) {
                //Filename to store
                $fileNameToStore = "" . time() . '.' . $extension;
                //uplod image
                $file = $request->file('mmfile');
                $destinationPath = public_path('/uploads/');
                $file->move($destinationPath, $fileNameToStore);

            }
        }

        try {
            DB::beginTransaction();
            if ($fileNameToStore != "") {
                DB::table('blog_images')->insert(['auth_id' => auth()->user()->id, 'blogCategory_id' => $blogCategorys->id, 'title' => $fileNameToStore, 'createdDate' => new DateTime()]);
            }

            $rows = DB::update('update categories set title=?,showInfront=?,stats=?,orderb=?,created_at=?,updated_at=? where id=?', [$blogCategorys->title, $blogCategorys->showInfront, $blogCategorys->stats, $blogCategorys->orderb, new DateTime(), new DateTime(), $blogCategorys->id]);

            if ($rows == 1) {
                DB::commit();
                return redirect()->route('blog-category.index')->with('success', 'Update Successfull');
            } else {
            }

        } catch (\Exception $e) {
            return redirect()->route('blog-category.index')->with('error', 'Update Unsuccessfull. Error 1');
        }

        return redirect()->route('blog-category.index')->with('error', 'Update Unsuccessfull');
    }

    public function destroy($id)
    {
        $rows = DB::select("SELECT orderb FROM categories where isdeleted=0 and id=?", [$id]);
        if ($rows != null) {
            $row = DB::update("update categories set isdeleted=1,updated_at=? where id=?", [new DateTime(), $id]);
            if ($row == 1) {
                return 1;
            }
        }
        return 0;
    } */
}
