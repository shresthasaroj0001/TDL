<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use DateTime;

class EntryHeaderController extends Controller
{
    public function index()
    {
        $responses = DB::select("select entry_id, store_id, DATE_FORMAT(entry_date, '%Y-%b-%e : %a') AS formatted_entry_date, entry_date, total_price, hst_price from entry_header where is_deleted=0 and order_placed=1");
        return view('admin.entry_header.index')->with('list', $responses);
    }

    //update date of entry header table
    public function updateDate(Request $request)
    {
        $entryId = $request->entry_id;
        if (is_null($entryId) || empty($entryId) || !is_numeric($entryId)) {
            return 1;
        }

        $new_date = $request->new_date;
        if (is_null($new_date) || empty($new_date)) {
            return 1;
        }

        $row = DB::update("update entry_header set entry_date = ? where entry_id=?", [$new_date, $entryId]);
        if ($row == 1) {
            return 0;
        }else{
            return 1;
        }
    }

    public function select(Request $request)  {
        //Query String filtering
        //decide whether it is create or update
        $operationId=0;
        if ($request->has('operation')) {
            $var = $request->input('operation');

            if (is_null($var) || !is_numeric($var)) {
                return redirect()->route('dashboard')->with('error','operation selection not valid');
            }
            $operationId = (int) $var;
            if($operationId == 1 || $operationId == 2)
            {}
            else
            {
                return redirect()->route('dashboard')->with('error','operation selection not valid');
            }
        }else{
            return redirect()->route('dashboard')->with('error','operation selection not found');
        }


        return view('admin.entry_header.store_selection')->with(compact('operationId'));
        
        //1 for create
        //2 for update
        if($storeId == 1)
            return redirect()->route('entry-header.create',['store' => '1'])->with('error','operation selection not found');
        else if($storeId == 2)
            return redirect()->route('entry.item.create',['store' => '2','id'=>0])->with('error','operation selection not found');
        else
            return redirect()->route('dashboard')->with('error','operation selection not found');
    
    }

    public function create(Request $request)
    {
        //obtain the store id
        //Query String filtering
        $storeId = 0;
        if ($request->has('store')) {
            $var = $request->input('store');

            if (is_null($var) || !is_numeric($var)) {
                return redirect()->route('dashboard')->with('error','Store selection not valid');
            }
            $storeId = (int) $var;
            if($storeId == 1 || $storeId == 2)
            {}
            else
            {
                return redirect()->route('dashboard')->with('error','Store selection not valid');
            }
        }else{
            return redirect()->route('dashboard')->with('error','Store selection not found');
        }

        $responses = DB::select("select entry_id, cart_items,total_price,hst_price, DATE_FORMAT(entry_date, '%Y-%b-%e : %a') AS entry_date_formatted, entry_date, order_placed from entry_header where is_deleted=0 and store_id=? order by entry_id desc limit 1",[$storeId]);

        $lastOrder = "";
        $is_completed = 0;
        $order_id = "";
        $cart_items=0;
        $total_price = 0;
        $hst_price = 0;
        if ($responses != null) {
            $lastOrder = $responses[0]->entry_date_formatted;
            $is_completed = $responses[0]->order_placed;
            $order_id = $responses[0]->entry_id;
            $cart_items = $responses[0]->cart_items;
            $total_price = $responses[0]->total_price;
            $hst_price = $responses[0]->hst_price;
        }

        $Today =  Carbon::now('UTC')->setTimezone('America/Toronto');
        $TodayDate = $Today->format('Y-m-d');

        if ($responses == null || $is_completed == 1) {
            //go ahead with new date
            $utcTimenow = gmdate("Y/m/d H:i:s");

            $id = DB::table('entry_header')->insertGetId(
                ['entry_date' => $TodayDate, 'order_placed' => 0, 'total_price' => 0, 'hst_price' => 0, 'hst_price' => 0, 'created_date' => $utcTimenow, 'is_deleted' => 0, 'store_id'=> $storeId]
            );
            //redirect to
            return redirect()->route('entry.item.create', ['id'=>$id,'store'=> $storeId]);
        } 

        // return $order_id;
        // //situation when create order is called multiple time in the same day
        // if( $order_id != ""){
        //     $lastOrderDate = $responses[0]->entry_date;
        //     $lastOrderDateTime = (new DateTime($lastOrderDate))->format('Y-m-d');
            
        //     if($lastOrderDateTime==$TodayDate){
        //         ///continue with order
        //         // $order_id  redirect with it
        //         return redirect()->route('entry.item.create', [$order_id]);
        //     } 
        // }

        return view('admin.entry_header.confirmation')->with(compact('storeId','lastOrder', 'is_completed', 'order_id','cart_items','total_price','hst_price'));
    }

    //from Confirmation box
    public function delete($id)
    {
        if (is_null($id) || empty($id) || !is_numeric($id)) {
            return redirect()->route('dashboard')->with('error', "Invalid URL parameters.");
        }

        $entry_id = (int) $id;

        $responses = DB::select("select order_placed from entry_header where is_deleted=0 and entry_id = ?",[$entry_id]);
        if($responses == null)
        {
            //no record exist
            return redirect()->route('dashboard')->with('error', "Invalid input. Record does not exist");
        }else{
            if($responses[0]->order_placed == 1)
            {
                return redirect()->route('dashboard')->with('error', "Invalid selection. Order completed cannot be deleted");
            }
        }

        $row = DB::update("update entry_header set is_deleted=1,updated_at=? where entry_id=?", [gmdate("Y/m/d H:i:s"), $entry_id]);
        if ($row == 1) {
            return redirect()->route('dashboard')->with('success', "Delete successful");
        }else{
            return redirect()->route('dashboard')->with('error', "Delete failed");
        }
    }

    //delete from jQuery
    public function deleteEntry($id)
    {
        if (is_null($id) || empty($id) || !is_numeric($id)) {
            return 0;
        }

        $entry_id = (int) $id;

        $responses = DB::select("select order_placed from entry_header where entry_id = ?",[$entry_id]);
        if($responses == null)
        {
            return 0; //no record exist
        }

        $row = DB::update("update entry_header set is_deleted=1, updated_at=? where entry_id=?", [gmdate("Y/m/d H:i:s"), $entry_id]);
        if ($row == 1) {
            return 1;
        }else{
            return 0;
        }
    }

    public function complete($id)
    {
        if (is_null($id) || empty($id) || !is_numeric($id)) {
            return redirect()->route('dashboard')->with('error', "Invalid URL parameters.");
        }

        $entry_id = (int) $id;

        $responses = DB::select("select order_placed from entry_header where is_deleted=0 and entry_id = ?",[$entry_id]);
        if($responses == null)
        {
            //no record exist
            return redirect()->route('dashboard')->with('error', "Invalid input. Record does not exist");
        }else{
            if($responses[0]->order_placed == 1)
            {
                return redirect()->route('dashboard')->with('error', "Invalid selection. Order already marked completed");
            }
        }

        $row = DB::update("update entry_header set order_placed = 1,updated_at=? where entry_id=?", [gmdate("Y/m/d H:i:s"), $entry_id]);
        if ($row == 1) {
            return redirect()->route('dashboard')->with('success', "Order marked successfull");
        }else{
            return redirect()->route('dashboard')->with('error', "Failed to mark order complete");
        }
    }

    public function edit($id, Request $request)
    {
        //validation
        if (is_null($id) || empty($id) || !is_numeric($id)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $entry_id = (int) $id;

        $storeId = 0;
        if ($request->has('store')) {
            $var = $request->input('store');

            if (is_null($var) || !is_numeric($var)) {
                return redirect()->route('dashboard')->with('error','Store selection not valid');
            }
            $storeId = (int) $var;
            if($storeId == 1 || $storeId == 2)
            {}
            else
            {
                return redirect()->route('dashboard')->with('error','Store selection not valid');
            }
        }else{
            return redirect()->route('dashboard')->with('error','Store selection not found');
        }

        return redirect()->route('entry.item.index',['id'=> $entry_id, 'store'=> $storeId]);
    }
}