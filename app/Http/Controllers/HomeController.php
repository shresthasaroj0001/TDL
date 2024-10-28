<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Exception;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    public function index_data(Request $request)
    {
        try {
            
            //line chart
            $lineChart = array();
            $sql = "WITH entrydateList AS( SELECT entry_date FROM entry_header WHERE order_placed = 1 AND is_deleted = 0 GROUP BY entry_date ORDER BY entry_date DESC LIMIT 10) select store_id, DATE_FORMAT(entry_header.entry_date, '%b-%e') 'OrderDate' , cast(total_price+hst_price as decimal(10,2)) as 'dailysum' from entry_header inner join entrydateList on entry_header.entry_date=entrydateList.entry_date where order_placed = 1 AND is_deleted = 0 order by entry_header.entry_date";

            $responses = DB::Select($sql);
            if ($responses != null) {
                $orderDates = array_unique(array_column($responses, 'OrderDate'));
                $store1 = array();
                $store2 = array();
                $labels = array();

                foreach ($orderDates as $dates) {
                    array_push($labels, (string) $dates);

                    $store1Date = array_values(array_filter($responses, function ($obj) use ($dates) {
                        if (isset($obj->store_id) && ($obj->store_id == 1) && ($obj->OrderDate == $dates)) {
                            return true;
                        }
                        return false;
                    }));

                    $store1OrderValue = 0;
                    if (!empty($store1Date)) {
                        $store1OrderValue = $store1Date[0]->dailysum;
                    }
                    array_push($store1, $store1OrderValue);

                    $store2Date = array_values(array_filter($responses, function ($obj) use ($dates) {
                        if (isset($obj->store_id) && ($obj->store_id == 2) && ($obj->OrderDate == $dates)) {
                            return true;
                        }
                        return false;
                    }));

                    $store2OrderValue = 0;
                    if (!empty($store2Date))
                        $store2OrderValue = $store2Date[0]->dailysum;
                    array_push($store2, $store2OrderValue);
                }

                $lineChart = array(
                    "aLabels" => $labels,
                    "aStore1" => $store1,
                    "aStore2" => $store2
                );
            }

            //bar chart
            $sql = "WITH entrydateMonth AS( select DATE_FORMAT(entry_header.entry_date, '%Y-%b') 'OrderDate' from entry_header WHERE order_placed=1 AND is_deleted=0 group by DATE_FORMAT(entry_header.entry_date, '%Y-%b') order by entry_date desc limit 5) select sum(total_price+hst_price) 'monthOrderValue', store_id ,DATE_FORMAT(entry_header.entry_date, '%Y-%b') 'OrderDate' from entry_header inner join entrydateMonth on DATE_FORMAT(entry_header.entry_date, '%Y-%b') = entrydateMonth.OrderDate where order_placed = 1 AND is_deleted = 0 group by DATE_FORMAT(entry_header.entry_date, '%Y-%b'), entry_header.store_id order by entry_header.entry_date";

            $responses = DB::Select($sql);
            $BarChart = array();
            if ($responses != null) {
                $orderDates = array_unique(array_column($responses, 'OrderDate'));
                $store1 = array();
                $store2 = array();
                $labels = array();

                foreach ($orderDates as $dates) {
                    array_push($labels, (string) $dates);

                    $store1Date = array_values(array_filter($responses, function ($obj) use ($dates) {
                        if (isset($obj->store_id) && ($obj->store_id == 1) && ($obj->OrderDate == $dates)) {
                            return true;
                        }
                        return false;
                    }));

                    $store1OrderValue = 0;
                    if (!empty($store1Date)) {
                        $store1OrderValue = $store1Date[0]->monthOrderValue;
                    }
                    array_push($store1, $store1OrderValue);

                    $store2Date = array_values(array_filter($responses, function ($obj) use ($dates) {
                        if (isset($obj->store_id) && ($obj->store_id == 2) && ($obj->OrderDate == $dates)) {
                            return true;
                        }
                        return false;
                    }));

                    $store2OrderValue = 0;
                    if (!empty($store2Date))
                        $store2OrderValue = $store2Date[0]->monthOrderValue;
                    array_push($store2, $store2OrderValue);
                }

                $BarChart = array(
                    "bLabels" => $labels,
                    "bStore1" => $store1,
                    "bStore2" => $store2
                );
            }

            return array(
                "LineChart" => $lineChart,
                "BarChart" => $BarChart
            );

        } catch (\Exception $e) {
            return $e;
            return array(
                "LineChart" => array(),
                "BarChart" => array()
            );
        }
    }
}
