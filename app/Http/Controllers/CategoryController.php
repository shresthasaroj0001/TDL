<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use DB;
use Validator;

class CategoryController extends Controller
{
    //category
    //type_id = 1
    public function indexx()
    {
        return view('admin.category.indexx');
    }

    public function index($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeid = (int) $setting;

        if ($typeid == 1)
            $name = "Period Name";
        else if ($typeid == 2)
            $name = "Expense Category";
        else {
            return redirect()->route('setting_name')->withInput()->with('error', "Settings Name not listed");
        }

        $responses = DB::select("SELECT category_id, name, description FROM category WHERE is_deleted=0 and type_id=? order by category_id desc", [$typeid]);

        return view('admin.category.index')->with('list', $responses)->with('typeid', $setting)->with('setting_name', $name);
    }

    public function create($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeid = (int) $setting;

        if ($typeid == 1)
            $name = "Period Name";
        else if ($typeid == 2)
            $name = "Expense Category";
        else {
            return redirect()->route('setting_name')->withInput()->with('error', "Settings Name not listed");
        }

        return view('admin.category.create')->with('typeid', $setting)->with('setting_name', $name);
    }

    public function store($setting, Request $request)
    {
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeid = (int) $setting;

        if ($typeid >= 1 && $typeid <= 2) {
        } else {
            return redirect()->route('setting_name')->withInput()->with('error', "Settings Name not listed");
        }

        $Validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:240',
                'body' => 'max:240'
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
            $ress = DB::select("SELECT category_id FROM category WHERE is_deleted=0 and type_id=? and name=?", [$typeid, $name]);
            if ($ress != null) {
                //return $ress;
                return redirect()->route('setting.name.create',[$typeid])->withInput()->with('error', "Failed. Please use different name");
            }

            $description = $request->body;
            if (is_null($description) || empty($description)) {
                $description = "";
            }

            DB::table('category')->insert(
                ['name' => $name, 'description' => $description, 'type_id' => $typeid, 'is_deleted' => 0]
            );

            return redirect()->route('setting.name.index',[$typeid])->withInput()->with('success', "Added successfully");
        } catch (\Exception $e) {
            return redirect()->route('setting.name.create',[$typeid])->withInput()->with('error', "Failed. Please try again");
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
