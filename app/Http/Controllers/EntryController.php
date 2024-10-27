<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use DB;
use Validator;
use Carbon;
use Jenssegers\Agent\Facades\Agent;

class EntryController extends Controller
{
    public function indexx()
    {
        return view('admin.entry.indexx');
    }

    private function getMyList($typeid)
    {
        if ($typeid == 1)
            return "Item";
        else
            return "Invalid";
    }

    public function index($item, Request $request)
    {
        // $insert_data = [];
        // for ($i = 1; $i <= 54; $i++) {
        
        //     for ($j = 1; $j <= 15; $j++)
        //     {
        //         $k = rand(1,106);
        //         if($i == $k)
        //             continue;
        //         $data = [
        //             'entry_id'                   => $i,
        //             'category_list_id'                  => $k, 
        //             'quantity'               => rand(1, 9),
        //             'rate' => rand(10.0, 100.0),
        //             'created_date' => gmdate("Y/m/d H:i:s")
        //         ];
        //         $insert_data[] = $data;
        //     }
        // }
        // $insert_data = collect($insert_data); // Make a collection to use the chunk method
        // $chunks = $insert_data->chunk(500);
        // foreach ($chunks as $chunk)
        // {
        //    DB::table('tbl_entry')->insert($chunk->toArray());
        // }
        // return "hello";

        //validation
        if (is_null($item) || empty($item) || !is_numeric($item)) {
            return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
        }
        $entry_id = (int) $item;

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

        $responses = DB::select("select order_placed from entry_header where is_deleted=0 and store_id=? and entry_id = ?", [$storeId, $entry_id]);
            if ($responses == null) {
                return redirect()->route('dashboard')->with('error', "Invalid input. Pending order list does not exist");
            } 
            $order_placed = $responses[0]->order_placed;
        

        // else {
        //     if ($responses[0]->order_placed == 1) {
        //         return redirect()->route('dashboard')->with('error', "Order already marked completed");
        //     }
        // }

        //obtain previous placed order detail
        $previousOrderResponses = DB::select("select entry_id, DATE_FORMAT(entry_date, '%b-%e : %a') 'OrderDate'  from entry_header where is_deleted=0 and entry_header.order_placed=1 and entry_header.store_id=".$storeId." and entry_header.entry_id < ".$entry_id." order by entry_id desc limit 2");

        $EntryIdsToQuery = array();
        array_push($EntryIdsToQuery, $entry_id);
        if ($previousOrderResponses != null) {
            foreach ($previousOrderResponses as $prevOrderInfo) {
                array_push($EntryIdsToQuery, $prevOrderInfo->entry_id);
            }
        }

        // SUBSTRING(category_list.NAME, 1, 40) as 'NAME'
        $sql = "WITH productlist AS (SELECT category_list.NAME, category_list.description, category.NAME AS category_name, category_list.price, category_list.hst_enforced, category_list.category_list_id FROM category INNER JOIN category_list ON category.category_id = category_list.category_id AND category_list.is_deleted = 0 AND category.is_deleted = 0 AND category.type_id = 1), rankTable AS (SELECT productlist.category_list_id,  COALESCE(sum(tbl_entry.quantity),0) as orderQuantity from productlist left join tbl_entry on productlist.category_list_id=tbl_entry.category_list_id ";
        
        if ($previousOrderResponses != null) {
            $sql .= "And tbl_entry.entry_id in (";
            
            $ids = array_map(function($item) {
                return $item->entry_id;
            }, $previousOrderResponses);
            
            $idString = implode(',', array_map(function($id) {
                return htmlspecialchars($id);
                // return "'" . htmlspecialchars($id) . "'";
            }, $ids));
            
            $sql .= $idString;
            $sql .= ') ';
        }

        $sql .= "left join entry_header on tbl_entry.entry_id=entry_header.entry_id And entry_header.is_deleted=0 And entry_header.order_placed=1 group by productlist.category_list_id ), itemordered AS (SELECT category_list_id, tbl_entry.quantity, tbl_entry_id, entry_id FROM tbl_entry where entry_id in (".implode(",", $EntryIdsToQuery).") ) SELECT product.NAME AS 'NAME', product.description, product.category_name, product.price, product.hst_enforced, product.category_list_id, rankTable.orderQuantity as 'rank', ";
        
        $sql .= "COALESCE(SUM(CASE WHEN entry_id = ".$entry_id." THEN quantity END),0) AS quantity, COALESCE(MAX(CASE WHEN entry_id = ".$entry_id." THEN tbl_entry_id END),0) AS tbl_entry_id ";
        
        if ($previousOrderResponses != null) {
            foreach ($previousOrderResponses as $index=>$prevOrderInfo) {
                $prevEntryId = $prevOrderInfo->entry_id;
                $prevEntryId = htmlspecialchars($prevEntryId); // Sanitize input to avoid SQL injection
                $sql .= ", COALESCE(SUM(CASE WHEN entry_id = ".$prevEntryId." THEN quantity END),'') AS item".($index+1);
            }
        }
 
        $sql .= " FROM productlist product inner join rankTable on product.category_list_id = rankTable.category_list_id LEFT JOIN itemordered ON product.category_list_id = itemordered.category_list_id group by product.category_list_id, orderQuantity";

        // return $sql;
        //CREATE INDEX idx_tbl_entry_entry_category ON tbl_entry(entry_id, category_list_id);
        $ItemList = DB::SELECT($sql);

        return view('admin.entry.index2')->with('order_placed',$order_placed)->with('itemlist', $ItemList)->with('entry_id',$entry_id)->with('previousOrderResponses', $previousOrderResponses)->with('storeId',$storeId);

        // if(Agent::isMobile())
        // {
        //     return view('admin.entry.index2')->with('order_placed',$order_placed)->with('itemlist', $ItemList)->with('entry_id',$entry_id)->with('previousOrderResponses', $previousOrderResponses)->with('storeId',$storeId);
        // }else{
        //     return view('admin.entry.index')->with('order_placed',$order_placed)->with('itemlist', $ItemList)->with('entry_id',$entry_id)->with('previousOrderResponses', $previousOrderResponses)->with('storeId',$storeId);
        // }
    }

    public function create($id, Request $request)
    {   
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
        } else {
            return redirect()->route('dashboard')->with('error','Store selection not found');
        }

        $responses = DB::Select("select order_placed, entry_id from entry_header where is_deleted=0 and store_id=? order by entry_id desc limit 1",[$storeId]);
        if ($responses == null) {
            return redirect()->route('entry-header.create',['store'=>$storeId]);//->with('error', "Internal error. Record not found");
        } else {
            if ($responses[0]->order_placed == 1) {
                return redirect()->route('dashboard')->with('error', "You do not have pending order. Please create a new order");
            }else{
                return redirect()->route('entry.item.index',['id'=>$responses[0]->entry_id, 'store'=> $storeId]);
            }
        }
    }

    public function preview(Request $request, $id)
    {
        $entry_id = (int) $id;
    
        $sql="WITH orderitems AS(SELECT category_list_id, quantity, rate, tbl_entry_id FROM tbl_entry WHERE tbl_entry.entry_id = ".$entry_id."), orderitemswithcategory AS (SELECT category_list.NAME, category_list.description, category.NAME AS category_name, category_list.hst_enforced, category_list.category_list_id, CAST((orderitems.quantity * orderitems.rate) AS decimal(10,2)) AS 'subTotal', CASE WHEN hst_enforced = 1 THEN CAST((orderitems.quantity * orderitems.rate * 0.13) AS decimal(10,2)) ELSE 0 END AS 'hst_calculated', orderitems.quantity, orderitems.rate, orderitems.tbl_entry_id FROM category INNER JOIN category_list ON category.category_id = category_list.category_id AND category.type_id = 1 INNER JOIN orderitems ON category_list.category_list_id = orderitems.category_list_id) SELECT NAME, Cast(rate AS DECIMAL) AS 'rate', category_list_id, category_name, description, hst_calculated, hst_enforced, quantity, subtotal FROM orderitemswithcategory ORDER BY tbl_entry_id";

        $responses = DB::Select($sql);
        $totalItem=0;
        $subTotal=0;
        $HSTTotal=0;

        if($responses != null)
        {
            $subTotal = array_reduce($responses, function($sum, $value) {
                return $sum + floatval($value->subTotal);
            }, 0);

            $HSTTotal = array_reduce($responses, function($sum, $value) {
                return $sum + floatval($value->hst_calculated);
            }, 0);

            $totalItem = count($responses);
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalItem),
            "recordsFiltered" => intval(0),
            "data" => $responses
        );

        return $json_data;
    }

    public function next($id)
    {
        $entry_id = (int) $id;

        $sql="WITH orderItems AS(select category_list_id, quantity, rate from tbl_entry where tbl_entry.entry_id=".$entry_id."), OrderItemsWithCategory as ( SELECT category_list.NAME, category_list.description, category.NAME AS category_name, category_list.hst_enforced, category_list.category_list_id, orderItems.quantity*orderItems.rate as 'subTotal', CASE WHEN hst_enforced = 1 THEN orderItems.quantity * orderItems.rate * 0.13 ELSE 0 END AS 'hst_calculated', orderItems.quantity, orderItems.rate FROM category INNER JOIN category_list ON category.category_id = category_list.category_id AND category.type_id = 1 INNER JOIN orderItems on category_list.category_list_id=orderItems.category_list_id) Select NAME, cast(rate as decimal) as 'rate', category_list_id, category_name, description, hst_calculated, hst_enforced, quantity, subtotal from OrderItemsWithCategory order by category_name";

        $responses = DB::Select($sql);
        $responseObject = [];
        $totalItem=0;
        $subTotal=0;
        $HSTTotal=0;

        foreach ($responses as $result) {
            $object = (object) [
            'NAME' => $result->NAME,
            'description' => $result->description,
            'category_name' => $result->category_name, 
            'rate' => (float) $result->rate,
            'category_list_id' => (float) $result->category_list_id,
            'hst_calculated' => (float) $result->hst_calculated,
            'hst_enforced' => (int) $result->hst_enforced,
            'quantity' => (float) $result->quantity,
            'subTotal' => (float) $result->subTotal
            ];

            $totalItem +=1;
            $subTotal +=(float) $result->subTotal;
            $HSTTotal += (float) $result->hst_calculated;
            
            array_push($responseObject, $object);
        }

        return view('admin.entry.next')->with('itemlist', $responseObject)->with('entry_id',$entry_id)->with('totalItem', $totalItem)->with('subTotal',$subTotal)->with('HSTTotal',$HSTTotal);

    }

    public function store($setting, Request $request)
    {
        try {
            if (is_null($setting) || empty($setting) || !is_numeric($setting)) {
                return redirect()->route('dashboard')->withInput()->with('error', "Invalid URL parameters.");
            }

            $entry_id = (int) $setting;

            $responses = DB::select("select order_placed from entry_header where is_deleted=0 and entry_id = ?", [$entry_id]);
            if ($responses == null) {
                return redirect()->route('entry.item.store', [$entry_id])->with('error', "Invalid input. Pending order list does not exist");
            }

            $cart_items =(int) $request->cart_total_items;
            $hst_price =(float) $request->hst_price;
            $total_price =(float) $request->total_price;

            $price = (float) $request->price;
            $category_list_id = $request->category_list_id;
            $entry_item_id = (int) $request->entry_item_id;
            $new_quantity = (float) $request->new_quantity;

            $returnVal=-1;
            DB::beginTransaction();
                $row = DB::update("update entry_header set cart_items=?, total_price=?, hst_price=?, updated_at=? where entry_id=?", [$cart_items, $total_price, $hst_price, gmdate("Y/m/d H:i:s"), $entry_id]);
                // if ($row != 1)
                //     return -1;

                if ($new_quantity > 0) {
                    if ($entry_item_id == 0) {
                        $id = DB::table('tbl_entry')->insertGetId(
                            ['entry_id' => $entry_id, 'category_list_id' => $category_list_id, 'quantity' => $new_quantity, 'created_date' => gmdate("Y/m/d H:i:s"), 'rate' => $price]
                        );

                        $returnVal = $id; //tbl_entry_id
                    } else {
                        $row = DB::update("update tbl_entry set quantity=?, rate=? where tbl_entry_id=? and entry_id=? and category_list_id=?", [$new_quantity, $price, $entry_item_id, $entry_id, $category_list_id]);
                        if ($row == 1)
                            $returnVal= 0;
                        else
                            $returnVal= -1;
                    }
                } else {
                    if ($entry_item_id > 0) {
                        $row = DB::delete('delete from tbl_entry where tbl_entry_id=?', [$entry_item_id]);
                        if ($row == 1)
                        $returnVal= 0;
                        else
                        $returnVal= -1;
                    } else {
                        //do nothing
                        //no a possible case
                        $returnVal= 0;
                    }
                }
            DB::commit();
            return $returnVal;
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
