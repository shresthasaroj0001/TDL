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
        if ($typeid == 1)
            return "Item";
        return "Invalid";
    }

    public function index($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 1) { //if ($categoryId == 2 || $categoryId == 3) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Category not listed");
        }

        $responses = DB::select("select category_list.category_list_id, category_list.name, category_list.description, category.name as category, hst_enforced, category_list_id, category_list.price from category inner join category_list on category.category_id=category_list.category_id WHERE category.is_deleted=0 and category_list.is_deleted=0 and category.type_id=? order by category_list_id desc, category_list_id desc", [$categoryId]);

        return view('admin.categorylist.index')->with('list', $responses)->with('categoryId', $categoryId)->with('categoryName', $this->getMyList($categoryId));
    }

    public function create($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 1) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Settings Name not listed");
        }

        //get category list
        $category = DB::select("SELECT category_id, name FROM category WHERE is_deleted=0 and type_id=?", [$categoryId]);
        if ($category == null) {
            return redirect()->route('setting.name.create', [$categoryId])->withInput()->with('error', "Failed. Please create" . $this->getMyList($categoryId));
        }
        return view('admin.categorylist.create')->with('categoryId', $categoryId)->with('categorylist', $category)->with('categoryName', $this->getMyList($categoryId));
    }

    public function store($setting, Request $request)
    {
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 1) {
        } else {
            return redirect()->route('setting.list.index', [$categoryId])->withInput()->with('error', "Category not listed");
        }

        $Validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:240',
                'body' => 'max:240',
                'category_id' => 'required|numeric',
                'hst_enforced' => 'required|in:0,1',
                'price' => 'required|numeric|min:0',
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
                return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please use different name");
            }

            $description = $request->body;
            if (is_null($description) || empty($description)) {
                $description = "";
            }

            $hst_enforced = $request->hst_enforced;
            if (is_null($hst_enforced) || empty($hst_enforced)) {
                $hst_enforced = 0;
            }

            $price = $request->price;
            if (is_null($price) || empty($price)) {
                $price = 0;
            }

            $category_id = $request->category_id; // id from category table
            DB::table('category_list')->insert(
                ['name' => $name, 'description' => $description, 'category_id' => $category_id, 'hst_enforced' => $hst_enforced, 'price' => $price, 'is_deleted' => 0]
            );

            return redirect()->route('setting.list.index', [$categoryId])->withInput()->with('success', "Added successfully");
        } catch (\Exception $e) {
            return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please try again");
        }
    }

    public function edit($setting, $list)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 1) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Settings Name not listed");
        }

        //validation
        if (is_null($list) || empty($list) || !is_numeric($list)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $category_list_id = (int) $list;

        $response = DB::select("SELECT * FROM category_list where is_deleted=0 and category_list_id=?", [$category_list_id]);
        if ($response == null) {
            return redirect()->route('dashboard')->withInput()->with('error', "Item not found.");
        }

        $categoryItem = (object) [];
        $categoryItem->category_list_id = $response[0]->category_list_id;
        $categoryItem->category_id = $response[0]->category_id;
        $categoryItem->name = $response[0]->name;
        $categoryItem->description = $response[0]->description;
        $categoryItem->hst_enforced = $response[0]->hst_enforced;
        $categoryItem->price = $response[0]->price;

        //get category list
        $category = DB::select("SELECT category_id, name FROM category WHERE is_deleted=0 and type_id=?", [$categoryId]);

        return view('admin.categorylist.edit')->with('item', $categoryItem)->with('categorylist', $category)->with('categoryName', $this->getMyList($categoryId))->with('categoryId', $categoryId);
    }

    public function update($setting, $list, Request $request)
    {
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 1) {
        } else {
            return redirect()->route('setting.list.index', [$categoryId])->withInput()->with('error', "Update operation could not be performed. Invalid URL parameters.");
        }

        if (is_null($list) || empty($list) || !is_numeric($list)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $category_list_id = (int) $list;

        $Validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:240',
                'body' => 'max:240',
                'category_id' => 'required|numeric',
                'hst_enforced' => 'required|in:0,1',
                'price' => 'required|numeric|min:0',
            ],
            $messages = [
                'required' => 'The :attribute field is required.',
            ]
        );

        if ($Validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($Validator);
        }

        $response = DB::select("SELECT name FROM category_list where is_deleted=0 and category_list_id=?", [$category_list_id]);
        if ($response == null) {
            return redirect()->back()->withInput($request->input())->withErrors($Validator)->with('error', 'Record not found');
        }

        $name = $request->name;
        $description = $request->body;
        $description = $request->body;
        if (is_null($description) || empty($description)) {
            $description = "";
        }
        $hst_enforced = $request->hst_enforced;
        $price = $request->price;
        $category_id = $request->category_id; // id from category table

        $rows = DB::update('update category_list set name=?, description=?, hst_enforced=?,price=?,category_id=? where category_list_id=?', [$name, $description, $hst_enforced, $price, $category_id, $category_list_id]);

        if ($rows == 1) {
            return redirect()->route('setting.list.index', [$setting])->with('success', 'Update Successfull');
        } else {
            return redirect()->route('setting.list.index', [$setting])->with('error', 'Update Unsuccessfull. Error 1');
        }
    }

    public function destroy($setting, $list)
    {
        if (is_null($list) || empty($list) || !is_numeric($list)) {
            return 0;
        }
        $category_list_id = (int) $list;

        $rows = DB::select("SELECT * FROM category_list where is_deleted=0 and category_list_id=?", [$category_list_id]);
        if ($rows != null) {
            $row = DB::update("update category_list set is_deleted=1 where category_list_id=?", [$category_list_id]);
            if ($row == 1) {
                return 1;
            }
        }
        return 0;
    }
}
