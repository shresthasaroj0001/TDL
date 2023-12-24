<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use DB;
use Validator;
use Carbon;

class EntryController extends Controller
{
    public function indexx()
    {
        return view('admin.entry.indexx');
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

    public function index($setting, Request $request)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('entry_list')->withInput()->with('error', "Invalid URL parameters.");
        }
        $typeId = (int) $setting;

        if ($typeId == 2 || $typeId == 3){}else
        {
            return redirect()->route('dashboard')->withInput()->with('error', "Category not listed");
        }

        //Query String filtering
        //check the period id
        $periodId = 0;
        if ($request->has('period')) {
            $var = $request->input('period');

            if (is_null($var) || !is_numeric($var)) {
                $var = 0;
            }
            $periodId = (int) $var;
        }else{
            // return "hi";
            $periodId = 999;
        }

        $categoryId = 0;
        if ($request->has('category_id')) {
            $var = $request->input('category_id');
            
            if (is_null($var) || empty($var) || !is_numeric($var)) {
                $var = 0;
            }
            $categoryId = (int) $var;
        }

        //find the category list
        $categoryListId = 0;
        if ($request->has('category_list_id')) {
            $var = $request->input('category_list_id');
            
            if (is_null($var) || empty($var) || !is_numeric($var)) {
                $var = 0;
            }
            $categoryListId = (int) $var;
        }

        $queryStringMessage = "";
        //logic for period
        //if 999, show latest 
        //else set 0 and display all
        $periods = DB::select("select category_id as id, name, description from category as tblPeriod where type_id=1 and is_deleted=0 order by category_id desc");

        if ($periods != null) {
            $periodIdOnly = array_column($periods, 'id');  //Only take id column from the list and form another array
            $found_key = array_search($periodId, $periodIdOnly);
            
            if (!is_numeric($found_key)) {
                $queryStringMessage = "Billing Period not found.<br>";
                
                if ($periodId == 999) // for latest selection
                {
                    $periodId = $periods[0]->id;
                }
            }
        } else {
            return redirect()->route('setting.name.create', [1])->withInput()->with('error', "Failed. Please create billing period");
        }

        $categories = DB::Select("select category_id as id, name, description from category where type_id=? and is_deleted=0", [$typeId]);
        if ($categories != null) {
            //return $ress;
            $categoriesOnly = array_column($categories, 'id');  //Only take id column from the list and form another array
            
            $found_key = array_search($categoryId, $categoriesOnly);
            if (!is_numeric($found_key)) {
                if (empty($queryStringMessage)) {
                    $queryStringMessage = "Category not found";
                } else {
                    $queryStringMessage = $queryStringMessage . "Category not found";
                }
                $categoryId = 0;
            }
        } else {
            return redirect()->route('setting.name.create', [$categoryId])->withInput()->with('error', "Failed. Please create " . $this->getMyList($typeId) . " category");
        }

        $sql = "select category_list_id as id, name, description from category_list where category_id IN (SELECT category_id from category where category.type_id=".$typeId.") and is_active=1";

        if($categoryId != 0)
            $sql .= " and category_id=".$categoryId;

        $sql .= " order by category_list_id desc, is_monthly";

        $categoryLists = DB::select($sql);
        if ($categoryLists != null) {
            if ($categoryListId != 0) {
                $categoriesOnly = array_column($categoryLists, 'id');  //Only take id column from the list and form another array
                $found_key = array_search($categoryListId, $categoriesOnly);

                if (!is_numeric($found_key)) {
                    $msg = $this->getMyList($typeId) . " not found in List.<br>";
                    if (empty($queryStringMessage)) {
                        $queryStringMessage = $msg;
                    } else {
                        $queryStringMessage .= " " . $msg;
                    }
                    $categoryListId = 0;
                }
            }
        } else {
            return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please create " . $this->getMyList($categoryId) . " headings");
        }

        $sql = 'With categoryList As ( select category.name as catName, category.category_id as catid, category_list.name, category_list.description, is_monthly, category_list_id from category_list inner join category on category_list.category_id=category.category_id where category.type_id='.$typeId.' ) select tblPeriod.category_id as tblPeriodId, tblPeriod.name as periodName, categoryList.catName, categoryList.catid, categoryList.name as catlist,categoryList.category_list_id, tbl_entry.entry_id, tbl_entry.ref_no, tbl_entry.created_at,tbl_entry.hst_amt,tbl_entry.total_amt,tbl_entry.description from tbl_entry inner join category as tblPeriod on tbl_entry.period_id=tblPeriod.category_id left join categoryList on tbl_entry.category_list_id=categoryList.category_list_id where ';

        $sql .= ' tbl_entry.is_deleted=0';
        if($periodId !=0)
        {
            $sql .= ' and tbl_entry.period_id='.$periodId;
        }
        if($categoryId !=0)
        {
            $sql .= ' and categoryList.catid='.$categoryId;
        }
        if($categoryListId !=0)
        {
            $sql .= ' and tbl_entry.category_list_id='.$categoryListId;
        }

        $sql .= ' order by tbl_entry.entry_id desc';
        
        $responses = DB::select($sql);

        //for total
        if($responses != null)
        {
            $hst_sum=0;
            $total_sum=0;
            foreach ($responses as $row) {
                $hst_sum += (float) $row->hst_amt;
                $total_sum+= (float) $row->total_amt;
            }
        }


        return view('admin.entry.index')->with('list', $responses)->with('typeId', $typeId)->with('categoryName', $this->getMyList($typeId))->with('periods',$periods)->with('periodId',$periodId)->with('categoryId',$categoryId)->with('categories',$categories)->with('categoryListId',$categoryListId)->with('categoryLists',$categoryLists)->with("hst_sum",$hst_sum)->with("total_sum",$total_sum);
    }

    public function create($setting)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $categoryId = (int) $setting;

        if ($categoryId == 2)
            $name = "Expense";
        else if ($categoryId == 3)
            $name = "Income";
        else {
            return redirect()->route('dashboard')->withInput()->with('error', "Settings Name not listed");
        }

        $periods = DB::select("select category_id as id, name, description from category as tblPeriod where type_id=1 and is_deleted=0 order by category_id desc");
        if ($periods != null) {
        }else{
            return redirect()->route('setting.name.create', [1])->withInput()->with('error', "Failed. Please create billing period");
        }

        $ress = DB::select("select category_list_id as id, name, description from category_list where category_id IN (SELECT category_id from category where category.type_id=?) and is_active=1 order by category_list_id desc, is_monthly",[$categoryId]);
        if ($ress != null) {
            //return $ress;
        }else{
            return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please create ".$name." category");
        }

        return view('admin.entry.create')->with('categoryId', $categoryId)->with('categoryName', $name)->with('periods',$periods)->with('list',$ress);
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
                'category_list_id' => 'required|numeric',
                'period' => 'required|numeric',
                'hst' => 'required|numeric|min:0',
                'total' => 'required|max:240|gte:hst',
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

            // $name = $request->name;
            // $ress = DB::select("SELECT name FROM category_list WHERE category_id=? and name=?", [$categoryId, $name]);
            // if ($ress != null) {
            //     //return $ress;
            //     return redirect()->route('setting.list.create', [$categoryId])->withInput()->with('error', "Failed. Please use different name");
            // }

            $description = $request->body;
            if (is_null($description) || empty($description)) {
                $description = "";
            }
            $ref_no = $request->ref_no;
            if (is_null($ref_no) || empty($ref_no)) {
                $ref_no = "";
            }

            $category_list_id = $request->category_list_id;
            $period = $request->period;
            $hst = $request->hst;
            $total = $request->total;
            $utcTimenow = gmdate("Y/m/d H:i:s");

            DB::table('tbl_entry')->insert(
                // entry_id`, `period_id`, `category_list_id`, `ref_no`, `created_at`, `hst_amt`, `total_amt`, `description`, `is_deleted
                ['period_id' => $period, 'description' => $description, 'category_list_id' => $category_list_id, 'ref_no' => $ref_no, 'hst_amt'=>$hst ,'total_amt'=>$total ,'is_deleted' => 0, 'created_at' => $utcTimenow]
            );

            return redirect()->route('entry.item.index', ['setting' => $categoryId,'period'=>$period])->withInput()->with('success', "Added successfully");
        } catch (\Exception $e) {
            return $e;
            return redirect()->route('entry.item.create', [$categoryId])->withInput()->with('error', "Failed. Please try again");
        }
    }

    public function destroy($entry, $item)
    {
        //typeId validation
        if (is_null($entry) || empty($entry) || !is_numeric($entry)) {
            return "Type id not valid";
        }
        $typeId = (int) $entry;

        if ($typeId == 2 || $typeId == 3) {
        } else {
            return "type id not match with system";
        }

        //entry_id validation
        if (is_null($item) || empty($item) || !is_numeric($item)) {
            return "entry id not valid";
        }
        $entry_id = (int) $item;

        if ($entry_id > 0) {
            $response = DB::select('SELECT category_list_id from tbl_entry WHERE is_deleted=0 and entry_id=?', [$entry_id]);
            if ($response != null) {

                //category List validation
                $categoryLists = DB::select('select category_list_id as id, name, description from category_list where category_id IN (SELECT category_id from category where category.type_id=?)', [$typeId]);

                $category_list_ids = array_column($categoryLists, 'id');  //Only take id column from the list and form another array
                $found_key = array_search($response[0]->category_list_id, $category_list_ids);
                
                if (!is_numeric($found_key)) {
                    return "Entry not match with category";
                }

                $utcTimenow = gmdate("Y/m/d H:i:s");
                $rows = DB::update("update tbl_entry set is_deleted=1,updated_at=? where entry_id=?", [$utcTimenow, $entry_id]);
                if ($rows == 1) {
                    return 1;
                }
            }
        }
        return "hello";
    }
}
