<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use DB;
use stdClass;

class OrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $Id;

    public function __construct($entryHeaderId)
    { 
        $this->Id = $entryHeaderId;
    }

    public function splitAtFirstDelimiter($string, $delimiter) {
        $delimiterIndex = strpos($string, $delimiter);
        
        if ($delimiterIndex === false) {
            // Delimiter not found, return original string
            return [$string, ''];
        }
        
        // Split the string into two parts
        return [
            substr($string, 0, $delimiterIndex),
            substr($string, $delimiterIndex + strlen($delimiter))
        ];
    }
    

    public function build()
    {
        $entry_id = (int) $this->Id;

        $sql = "WITH orderItems AS(select category_list_id, quantity, rate from tbl_entry where tbl_entry.entry_id=" . $entry_id . "), OrderItemsWithCategory as ( SELECT category_list.NAME, category_list.description, category.NAME AS category_name, category_list.hst_enforced, category_list.category_list_id, orderItems.quantity*orderItems.rate as 'subTotal', CASE WHEN hst_enforced = 1 THEN orderItems.quantity * orderItems.rate * 0.13 ELSE 0 END AS 'hst_calculated', orderItems.quantity, orderItems.rate FROM category INNER JOIN category_list ON category.category_id = category_list.category_id AND category.type_id = 1 INNER JOIN orderItems on category_list.category_list_id=orderItems.category_list_id) Select NAME, cast(rate as decimal) as 'rate', category_list_id, category_name, description, hst_calculated, hst_enforced, quantity, subtotal from OrderItemsWithCategory order by category_name";

        $responses = DB::Select($sql);

        // Example usage
        $string = "TING GRAPEFRUIT--24--300ML	N/A	2";
        $delimiter = '--';
        
        $finalData = array();

        foreach ($responses as $index => $orderInfo) {
            $result = $this->splitAtFirstDelimiter($orderInfo->NAME, $delimiter);
            $obj = new stdClass();
            $obj->NAME = $result[0];
            $obj->NAME2 = $result[1];
            $obj->category_name = $orderInfo->category_name;
            $obj->quantity = $orderInfo->quantity;

            array_push($finalData,$obj);
        }

        $StoreDetail = DB::Select("select *,DATE_FORMAT(entry_date, '%Y - %b - %e') AS formatted_entry_date from entry_header where entry_id=?", [$entry_id]);
        $storeId = $StoreDetail[0]->store_id;
        $entryDate = $StoreDetail[0]->formatted_entry_date;

        //email signature
        $signature = "";
        if($storeId == 1)
        { //Danforth
            $signature .= "<div>Unit-2, 1071 Danforth Rd, Scarborough</div>";
            $signature .= "<div>M1J 4P6, ON</div>";
            $signature .= "<div>Store Phone: 416-269-0202</div>";
        }else{
            $signature .= "ERROR";
        }

        return $this->from('info@themomostation.ca', 'The MOMO Station')
                ->replyTo('info@themomostation.ca', 'The MOMO Station')
                ->to('shresthasaroj0001@gmail.com')
                // ->cc('MOMO@1.co')
                ->bcc('practise.saroj@gmail.com')
                ->subject('Order Items - The MOMO Station')
                ->view('mail.order')
                ->with('itemlist', $finalData)->with('entryDate', $entryDate)->with('signature', $signature);
    }

    
    

}
