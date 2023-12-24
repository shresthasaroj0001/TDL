<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Exception;
use Validator;

class ReportController extends Controller
{
    private function getMyList($typeid)
    {
        if ($typeid == 2)
            return "Expenses";
        else if ($typeid == 3)
            return "Income";
        else
            return "Invalid";
    }

    public function test()
    {
        return view('admin.report.overview_test');
    }

    public function overview($setting, Request $request)
    {
        //validation
        if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters for report overview.");
        }
        $typeId = (int) $setting;

        if ($typeId == 2 || $typeId == 3) {
        } else {
            return redirect()->route('dashboard')->withInput()->with('error', "Type Id not listed");
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
        } else {
            // return "hi";
            $periodId = 999;
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

        $categoryLists = DB::select("select category_list_id as id, category_list.name as category_list_name, category.name as category_name, is_monthly from category_list inner join category on category_list.category_id=category.category_id where category.type_id=? order by category.name, category_list.name", [$typeId]);
        if ($categoryLists != null) {
        } else {
            return redirect()->route('setting.list.create', [$typeId])->withInput()->with('error', "Failed. Please create " . $this->getMyList($typeId) . " headings");
        }

        $sql = 'With categoryList As ( select category.name as catName, category.category_id as catid, category_list.name, category_list.description, is_monthly, category_list_id from category_list inner join category on category_list.category_id=category.category_id where category.type_id=' . $typeId . ' ) select tblPeriod.category_id as tblPeriodId, tblPeriod.name as periodName, categoryList.catName, categoryList.catid, categoryList.name as catlist,categoryList.category_list_id, tbl_entry.entry_id, tbl_entry.ref_no, tbl_entry.created_at,tbl_entry.hst_amt,tbl_entry.total_amt,tbl_entry.description from tbl_entry inner join category as tblPeriod on tbl_entry.period_id=tblPeriod.category_id left join categoryList on tbl_entry.category_list_id=categoryList.category_list_id where ';

        $sql .= ' tbl_entry.is_deleted=0';
        if ($periodId != 0) {
            $sql .= ' and tbl_entry.period_id=' . $periodId;
        }
        $sql .= ' order by tbl_entry.entry_id desc';
        $responses = DB::select($sql);

        $NotInList = array(); //find monthly item not in entry
        //for total
        if ($responses != null) {
            //checking for montly heading not in list
            $categoriesListId = array_column($responses, 'category_list_id');
            foreach ($categoryLists as $value) {
                if ($value->is_monthly == 1) {
                    if (!in_array($value->id, $categoriesListId)) {
                        array_push($NotInList, $value);
                    }
                }
            }
        }

        //grouping the entry list by category
        $list = array();
        $hst_sum = 0;
        $total_sum = 0;
        foreach ($responses as $index => $item) {
            $hst_sum += (float) $item->hst_amt;
            $total_sum += (float) $item->total_amt;

            $flag = false;
            if (count($list) > 0) {
                $keys = array_column($list, 'category');
                $indexx = array_search($item->catName, $keys);

                if ($indexx !== false) {
                    array_push($list[$indexx]['data'], $item);
                    $list[$indexx]['hst_amt'] += (float) $item->hst_amt;
                    $list[$indexx]['total_amt'] += (float) $item->total_amt;
                    $flag = true;
                }
            }

            if ($flag == false) {
                $obj = ["category" => $item->catName, 'hst_amt' => (float) $item->hst_amt, 'total_amt' => (float)$item->total_amt, 'data' => array($item)];
                array_push($list, $obj);
            }
        }

        $categoryName = $this->getMyList($typeId);
        // return $list;
        return view('admin.report.overview', compact('hst_sum', 'total_sum', 'NotInList', 'list', 'periods', 'periodId', 'typeId', 'categoryName'));

        // return view('admin.entry.index')->with('list', $responses)->with('typeId', $typeId)->with('categoryName', $this->getMyList($typeId))->with('periods',$periods)->with('periodId',$periodId)->with('categoryId',$categoryId)->with('categories',$categories)->with('categoryListId',$categoryListId)->with('categoryLists',$categoryLists)->with("hst_sum",$hst_sum)->with("total_sum",$total_sum);
    }

    public function getLastEntryOfaCategory(Request $request)
    {
        $isSuccess = 0;
        $response = [];

        try {
            //validation
            $categoryListId = 0;
            if ($request->has('category_list_id')) {
                $var = $request->input('category_list_id');

                if (is_null($var) || !is_numeric($var)) {
                    $var = 0;
                }
                $categoryListId = (int) $var;
            }

            if ($categoryListId == 0)
                throw new Exception("Invalid category list id");
            // $selectionsss = $request['selected'] == null ? [] : ($request['selected']);
            // $postId = $request->pid;

            $tblentry = DB::select("select period.name, tbl_entry.ref_no, hst_amt,total_amt from tbl_entry inner join category as period on tbl_entry.period_id=period.category_id where tbl_entry.category_list_id=? and tbl_entry.is_deleted=0 order by period.category_id desc limit 1", [$categoryListId]);

            $isSuccess = 1;
            $response = $tblentry;
        } catch (Exception $ex) {
        }
        $json_data = array(
            "isSuccess" => $isSuccess,
            "data" => $response
        );
        return $json_data;
    }

    public function StoreMonthyExpenseEntry($typeid, Request $request)
    {
        try {
            //typeId validation
            if (is_null($typeid) || empty($typeid) || !is_numeric($typeid)) {
                return "Type id not valid";
            }
            $typeId = (int) $typeid;

            if ($typeId == 2 || $typeId == 3) {
            } else {
                return "type id not match with system";
            }

            $Validator = Validator::make(
                $request->all(),
                [
                    'category_list_id' => 'required|numeric|gte:0',
                    'period' => 'required|numeric|gte:0',
                    'hst' => 'required|numeric|min:0',
                    'total' => 'required|max:240|gte:hst',
                ],
                $messages = [
                    'required' => 'The :attribute field is required.',
                ]
            );

            if ($Validator->fails()) {
                return redirect()->back()->withInput($request->input())->withErrors($Validator);
            }


            $category_list_id = $request->category_list_id;
            $period = $request->period;
            $hst = $request->hst;
            $total = $request->total;
            $utcTimenow = gmdate("Y/m/d H:i:s");

            $tblCategoryList = DB::select("select name from category_list where is_monthly=1 and is_active=1 and category_list_id=?", [$category_list_id]);
            if ($tblCategoryList == null) {
                return "Category List Not FOund";
            }

            DB::table('tbl_entry')->insert(
                // entry_id`, `period_id`, `category_list_id`, `ref_no`, `created_at`, `hst_amt`, `total_amt`, `description`, `is_deleted
                ['period_id' => $period, 'description' => '', 'category_list_id' => $category_list_id, 'ref_no' => '', 'hst_amt' => $hst, 'total_amt' => $total, 'is_deleted' => 0, 'created_at' => $utcTimenow]
            );

            // $selectionsss = $request['selected'] == null ? [] : ($request['selected']);
            // $postId = $request->pid;
            return 1;
        } catch (Exception $ex) {
            return $ex;
        }
        return 0;
    }
}