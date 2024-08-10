@extends('admin.master')
@section('mycss')

{{--
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css"> --}}
<style>
    .my_class {
        font-weight: bold
    }

    /* Example of overriding the hover effect */
    .categorySelected {
        background-color: lightgray !important;
    }

    .hstEnforced {
        color: red;
    }

    #mytable_filter {
        float: left;
        padding-left: 10px;
    }

    .responsive-table {
        max-width: 100%;
        overflow-x: auto;
        /* Allows horizontal scrolling if needed */
    }

    .editable-input {
        /* width: 50px; */
        /* Adjust based on your needs */
    }

    body {
        scroll-padding-top: 3rem;
        /* Match the top offset of your sticky element */
    }

    .dt-search {
        float: left
    }

    .modal-dialog.modal-fullscreen-sm-down {
        max-width: 1000px !important;
        /* Override */
        /* margin: 10px !important;  */
        /* Override */
    }

    input#dt-search-0 {
        width: inherit;
    }

    tr.dtrg-group{
        text-align: right;
        background-color: aquamarine !important;
    }

    tr.dtrg-group td:not(:first-child) {
        text-align: left;
    }

    tr.dtrg-group td {
        padding-left: 0px !important;
        font-weight: 700 !important;
    }

    span {
        cursor: pointer;
    }

    .number {
        margin: 0.5px;
        /* width: 20px !important;    		 */
    }

    .minus,
    .plus {
        width: 25px;
        /* height: inherit; */
        background: #f2f2f2;
        border-radius: 4px;
        /* padding: 8px 5px 8px 5px; */
        border: 1px solid #ddd;
        display: inline-block;
        vertical-align: middle;
        text-align: center;
    }

    input {
        /* height: inherit; */
        width: 20px;
        text-align: center;
        font-size: 26px;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: inline-block;
        vertical-align: middle;
    }

    td.dt-type-numeric {
        text-align: left !important;
        padding-right: 1px !important;
        padding-left: 1px !important;
    }

    #mytable tr td:first-child {
        padding-right: 1px !important;
        padding-left: 1px !important;
    }
 
    th[aria-label="Quantity"] {
        padding-right: 2px !important;
        padding-left: 2px !important;
        text-align: center !important
    }

    th[aria-label="Price"] {
        padding-right: 2px !important;
        text-align: left !important;
    }

    .qtytd {
        text-align: center !important;
    }

    th.dt-type-numeric {
        padding-right: 1px !important;
        padding-left: 1px !important;
        text-align: center !important;
    }

    #previewtable tr td:not(:first-child), th:not(:first-child) {
        text-align: right !important;
        padding-right: 10px !important;
    }

    #mytable tr th:not(:first-child) {
        text-align: center !important;
    }

</style>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css">
@endsection

@section('myscript')

{{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script> --}}
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js"></script>
<script src="/b/js/toastr.js"></script>

<script src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.js"></script>
<script defer src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
<script defer src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>
<script defer src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.colVis.min.js"></script>

<script defer src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
<script defer src="https://cdn.datatables.net/rowgroup/1.5.0/js/rowGroup.dataTables.js"></script>


<script src="/b/js/entry-index2.js"></script>
@endsection

@section('bodycontent')
<div class="container-fluid" style="padding-right: 5px; padding-left: 5px">
    <ol class="breadcrumb mb-4" style="margin-bottom: 10px !important;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Order Estimate
            @if ($storeId == 1)
                Danforth
            @else
                Markham
            @endif

        </li>
    </ol>
    @include('admin.messages')
    <div class="mb-4 sticky-top" style="position: -webkit-sticky; top: 3rem !important; z-index: 1000 !important; margin-bottom: 10px !important;">
        <div class="card mb-4" style="background-color: #e9ecef; color: #6c757d; margin-bottom: 10px !important;">
            <div class="card-body" style="padding-bottom: 0% !important">
                <div class="row headers" style="color: black">
                    <div class="col-md-3 col-sm-3 col" style="padding-left: 2px">
                        <p>Items: <b><span id="NumberOfItems"></span></b></p>
                    </div>
                    <div class="col-md-3 col-sm-3 col">
                        <p>Total: $<b><span id="totalPrice"></span></b></p>
                    </div>
                    <div class="col-md-3 col-sm-3 col">
                        <p>HST: $<b><span id="hstprice"></span></b></p>
                    </div>
                    <div class="col-md-3 col-sm-3 col" style="padding-right: 2px; text-align: -webkit-right;">
                        <button type="button" id="previewBtn" class="btn btn-primary btn-sm">Preview</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header" style="padding-bottom: 1%; padding-top: 1%;">
            <div class="row">
                <p style="margin-bottom: 5px !important"><span class="hstEnforced">* </span>H.S.T Applicable</p>
            </div>
        </div>
        <div class="card-body" style="padding: 0px !important">
            <div class="rows">
                <div class="table-responsive">
                    <table id="mytable" class="table responsive-table" style="width: 100%">
                        <thead class="sticky-tops">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th class="RestrictOrdering">Price</th>
                                <th class="hiddenCols">lastOrderedDate</th>
                                <th class="hiddenCols">Quantity</th>
                                <th class="RestrictOrdering">Quantity</th>
                                <th class="hiddenCols">HST</th>
                                <th class="hiddenCols">tbl_entry_id</th>
                                <th class="hiddenCols">category_list_id</th>
                                @foreach ($previousOrderResponses as $item)
                                <th class="RestrictOrdering" style="padding-left: 2px; padding-right: 2px; margin: 0px;">{{$item->OrderDate}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($itemlist as $index=>$item)
                            <tr @if ($item->quantity > 0)
                                class='categorySelected'
                                @endif
                                >
                                <td>{{$item->NAME}}
                                    @if ($item->hst_enforced == '1')
                                    <span class="hstEnforced">*</span>
                                    @endif
                                </td>
                                <td>{{$item->category_name}}</td>
                                <td>{{$item->price}}</td>
                                <td>{{$item->rank}}</td>
                                <td class="qty">{{$item->quantity}}</td>
                                <td class="qtytd" style="padding-left: 0px; padding-right: 0px; margin: 0px;">
                                    <div class="number" style="">
                                        <span class="minus">-</span>
                                        <input type="text" class="editable-input" value="{{$item->quantity}}" readonly />
                                        <span class="plus">+</span>
                                    </div>
                                    {{-- <i class="fa fa-minus-square faa-minus" aria-hidden="true"></i>
                                    <span class="editable-input">{{$item->quantity}}</span>
                                    <i class="fa fa-plus-circle faa-plus" aria-hidden="true"></i> --}}
                                </td>
                                <td>{{$item->hst_enforced}}</td>
                                <td>{{$item->tbl_entry_id}}</td>
                                <td>{{$item->category_list_id}}</td>
                                @if(isset($item->item1))
                                <td class="itemNumeric" style="text-align: center !important;padding-left: 0px; padding-right: 0px; margin: 0px;">{{$item->item1}}</td>
                                @endif
                                @if(isset($item->item2))
                                <td class="itemNumeric" style="text-align: center !important; padding-left: 0px; padding-right: 0px; margin: 0px;">{{$item->item2}}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal-entry" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-fullscreen-sm-down" role="document">
            <div class="modal-content" id="modalsss" style="width: 100%; margin: auto;">
                <div class="modal-header">
                    <h4 class="modal-title previewed" id="previewTotal"></h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body" style="margin: 0px !important; padding: 1px !important;">
                        <div class="table-responsive">
                        <table id="previewtable" class="no-wrap display" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Rate</th>
                                        <th>Quantity</th>
                                        <th>Sub-Total</th>
                                        <th>HST</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-warning" id="closeModalBtn"
                        data-dismiss="modal">Close</button>
                        @if ($order_placed == 0)
                        <a href="{{ route('next',[$entry_id]) }}"><button type="button" class="btn btn-primary" id="UpdateModalBtn">Ready to Order</button></a>
                        @endif
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_currentUrl" id="_currentUrl" value="{{ url()->current() }}">
@endsection