<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use DB;
use Validator;

class CategoryListController extends Controller
{
    public function indexx()
    {
        return view('admin.categorylist.indexx');
    }

    private function getMyList($typeid)
    {
        if ($typeid == 2)
            return "Expenses";
        else if ($typeid == 3)
            return "Income";
        else
            return "Invalid";
    }

    public function index($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 2 || $categoryId == 3) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Category not listed");
        }

        $responses = DB::select("select category_list.name, category_list.description, category.name as category,is_monthly, is_active, category_list_id from category inner join category_list on category.category_id=category_list.category_id WHERE category.type_id=? order by is_active desc, category_list_id desc", [$categoryId]);

        return view('admin.categorylist.index')->with('list', $responses)->with('categoryId', $categoryId)->with('categoryName', $this->getMyList($categoryId));
    }

    public function create($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 2 || $categoryId == 3) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Settings Name not listed");
        }

        //get category list
        $category = DB::select("SELECT category_id, name FROM category WHERE is_deleted=0 and type_id=?", [$categoryId]);
        if ($category == null) {
            return redirect()->route('setting.name.create', [$categoryId])->withInput()->with('error', "Failed. Please create".$this->getMyList($categoryId));
        }

        return view('admin.categorylist.create')->with('categoryId', $categoryId)->with('categorylist', $category)->with('categoryName', $this->getMyList($categoryId));
    }

    public function store($setting, Request $request)
    {
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 2 || $categoryId == 3) {
        } else {
            return redirect()->route('setting.list.index', [$categoryId])->withInput()->with('error', "Category not listed");
        }

        $Validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:240',
                'body' => 'max:240',
                'category_id' => 'required|numeric',
                'ismonthly' => 'required|in:0,1',
            ],
            $messages = [
                'required' => 'The :attribute field is required.',
            ]
        );

        if ($Validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($Validator);
        }

        try {

            $name = $request->name;
            $ress = DB::select("SELECT name FROM category_list WHERE category_id IN (SELECT category_id from category where category.type_id=?) and name=?", [$categoryId, $name]);
            
            if ($ress != null) {
                //return $ress;
                return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please use different name");
            }

            $description = $request->body;
            if (is_null($description) || empty($description)) {
                $description = "";
            }

            $ismonthly = $request->ismonthly;
            $category_id = $request->category_id;// id from category table
            DB::table('category_list')->insert(
                ['name' => $name, 'description' => $description, 'category_id' => $category_id, 'is_monthly' => $ismonthly, 'is_active' => 1]
            );

            return redirect()->route('setting.list.index', [$categoryId])->withInput()->with('success', "Added successfully");
        } catch (\Exception $e) {
            return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please try again");
        }
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
        $Validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|max:240',
                'mmfile' => 'mimes:jpeg,png,bmp,tiff |max:4096 ',
                'stats' => 'required|numeric',
                'orderb' => 'required|numeric',
                'showinfront' => 'required|numeric',
            ],
            $messages = [
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
    }
}
