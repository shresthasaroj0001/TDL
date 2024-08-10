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

    private function getMyList($typeid)
    {
        if ($typeid == 1)
            return "Category"; //food category
        // else if ($typeid == 2)
        //     return  "Expenses" ; 
        // else if ($typeid == 3)
        //     return "Income";
        // else if ($typeid == 4)
        //     return  "Expenses Reporting" ;
        // else if ($typeid == 5)
        //     return "Income Reporting";

        else
            return "Invalid";
    }

    public function index($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeid = (int) $setting;

        if ($typeid != 1) // if($typeid > 5 || $typeid < 1)
        {
            return redirect()->route('setting_name')->withInput()->with('error', "Master settings not listed");
        }

        $responses = DB::select("SELECT category_id as id, name, description FROM category WHERE is_deleted=0 and type_id=? order by category_id desc", [$typeid]);

        return view('admin.category.index')->with('list', $responses)->with('typeid', $setting)->with('setting_name', $this->getMyList($typeid));
    }

    public function create($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeid = (int) $setting;

        if ($typeid > 5 || $typeid < 1)
            return redirect()->route('setting_name')->withInput()->with('error', "Settings Name not listed");

        return view('admin.category.create')->with('typeid', $setting)->with('setting_name', $this->getMyList($typeid));
    }

    public function store($setting, Request $request)
    {
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('setting_name')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeid = (int) $setting;

        if ($typeid >= 1 && $typeid <= 5) {
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
                return redirect()->route('setting.name.create', [$typeid])->withInput()->with('error', "Failed. Please use different name");
            }

            $description = $request->body;
            if (is_null($description) || empty($description)) {
                $description = "";
            }

            DB::table('category')->insert(
                ['name' => $name, 'description' => $description, 'type_id' => $typeid, 'is_deleted' => 0]
            );

            return redirect()->route('setting.name.index', [$typeid])->withInput()->with('success', "Added successfully");
        } catch (\Exception $e) {
            return redirect()->route('setting.name.create', [$typeid])->withInput()->with('error', "Failed. Please try again");
        }
    }

    public function edit($setting, $name)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeId = (int) $setting;

        if ($typeId == 1) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Settings Name not listed");
        }

        //validation
        if (is_null($name) || empty($name) || !is_numeric($name)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $category_id = (int) $name;

        $response = DB::select("SELECT category_id as id, name, description FROM category WHERE is_deleted=0 and type_id=? and category_id=?", [$typeId, $category_id]);
        if ($response == null) {
            return redirect()->route('dashboard')->withInput()->with('error', "Item not found.");
        }

        $categoryItem = (object) [];
        $categoryItem->id = $response[0]->id;
        $categoryItem->name = $response[0]->name;
        $categoryItem->description = $response[0]->description;

        return view('admin.category.edit')->with('item', $categoryItem)->with('typeid', $typeId)->with('setting_name', $this->getMyList($typeId));
    }

    public function update($setting, $name, Request $request)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeId = (int) $setting;

        if ($typeId == 1) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Settings Name not listed");
        }

        //validation
        if (is_null($name) || empty($name) || !is_numeric($name)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $category_id = (int) $name;


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

        $response = DB::select("SELECT category_id as id, name, description FROM category WHERE is_deleted=0 and type_id=? and category_id=?", [$typeId, $category_id]);
        if ($response == null) {
            return redirect()->route('dashboard')->withInput()->with('error', "Item not found.");
        }

        $categoryItem = (object) [];
        $categoryItem->category_id = $category_id;
        $categoryItem->name = $request->name;
        $description = $request->body;

        if (is_null($description) || empty($description)) {
            $description = "";
        }

        $rows = DB::update('update category set name=?, description=?, type_id=? where category_id=?', [$categoryItem->name, $description, $typeId, $categoryItem->category_id]);

        if ($rows == 1) {
            return redirect()->route('setting.name.index', [$typeId])->with('success', 'Update Successfull');
        }

        return redirect()->route('setting.name.index', [$typeId])->with('error', 'Update Unsuccessfull');
    }

    public function destroy($setting, $name)
    {
        if (is_null($name) || empty($name) || !is_numeric($name)) {
            return 0;
        }
        $category_id = (int) $name;

        $rows = DB::select("SELECT * FROM category where is_deleted=0 and category_id=?", [$category_id]);
        if($rows != null)
        {
            $row = DB::update("update category set is_deleted=1 where category_id=?", [$category_id]);
            if ($row == 1)
                return 1;
        }
        return 0;
    }
}
