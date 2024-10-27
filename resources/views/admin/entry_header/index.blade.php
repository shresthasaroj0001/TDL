@extends('admin.master')
@section('mycss')

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    div.dt-container div.dt-layout-row {
        display: inline-table;
    }
</style>
{{-- https://www.daterangepicker.com/#config --}}
@endsection

@section('myscript')
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js"></script>

<script>
    $(function () {
        $("#example1").DataTable({
            "order": [0,'desc'],
            "ordering": true,
            columnDefs: [
                { targets: "hiddenCols", visible: false },
                { targets: "RestrictOrdering", orderable: false },
            ],
        });

        

        // $("#modal-entry").modal("show");

        $('input[name="birthday"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            "locale": {
                "format": "YYYY-MM-DD"
            },
            maxDate : moment(),
            //  maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#example1").on("click", ".action-delete", function () {
            // $("#mytbl .action-delete").click(function () {
            button = null;
            button = $(this);
            //(button.attr('rowid'));
            
            var result = confirm("Are You Sure You want to delete ?");
            if (result) {
                var i = $('#_currentUrl').val() + "/" + button.attr("rowid");
                $.ajax({
                headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
                },
                url: i,
                type: "Delete",
                success: function (ddata) {
                    if (ddata == 0) {
                        alert("Internal Error");
                        return false;
                    }
            
                    if (ddata == 1) {
                        let currentTR = button.closest("tr");
                        currentTR.addClass("Row4Delete");
                        if (currentTR.hasClass("child")) {
                        prevTR = currentTR.prev();
                        prevTR.addClass("Row4Delete");
                        }
                    
                        $(".Row4Delete").remove();
                    }},
                //fail
                });
            }
        });

    });

    $('input[name="birthday"]').on('apply.daterangepicker', function(ev, picker) {
    //do something, like clearing an input
        var newDate = picker.startDate.format('YYYY-MM-DD');
        var $this = $(this);
        var rowId = $this.data('entry-id');
        // console.log(rowId);
        // console.log("--------");
        
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
            },
            url: $('#_currentUrl').val(),
            type: "POST",
            data: {
                entry_id: rowId,
                new_date : newDate
            },
            success: function (ddata) {
                console.log(ddata);
                if(ddata == 0)
            {
                alert('Date updates success');
                location.reload();
            }

            },
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            fail: function (ddata) {
                alert("Error while processing your request");
            },
        });
    });

</script>
@endsection


@section('bodycontent')
<div class="container-fluid px-1">
    {{-- <h1 class="mt-4">Item Category</h1> --}}
    <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col"> <i class="fas fa-table me-1"></i>
                    List of Orders Placed</div>
                <div class="col" style="text-align: right;">
                    <a href="{{route('entry-header.create')}}"><button class="btn btn-primary"></button></a>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table id="example1" class="table no-wrap display" style="width: 100%">
                <thead>
                    <tr>
                        <th>Order Date</th>
                        <th>Store</th>
                        <th>Subtotal</th>
                        <th>HST</th>
                        <th>Update Date</th>
                        <th class="RestrictOrdering">-</th>
                        <th class="hiddenCols RestrictOrdering">-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>
                            <a href="{{route('entry-header.edit',['id'=>$item->entry_id, 'store'=>$item->store_id])}}">
                                {{$item->formatted_entry_date}}</a>
                        </td>
                        <td>
                            @if ($item->store_id == 1)
                            Danforth
                            @elseif ($item->store_id == 2)
                            Markham
                            @else
                            -
                            @endif
                        </td>
                        <td>{{$item->total_price}}</td>
                        <td>{{$item->hst_price}}</td>
                        <td>
                            <input type="text" name="birthday" value="{{$item->entry_date}}"
                                data-entry-id="{{ $item->entry_id }}" />
                        </td>
                        <td>
                            {{-- <button class="btn btn-primary action-delete" rowid="{{$item->entry_id}}"><i
                                    class="fa fa-pen" aria-hidden="true"></i></button> --}}
                            <button class="btn btn-danger action-delete" rowid="{{$item->entry_id}}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                        <td>{{$item->entry_id}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
        <input type="hidden" name="_currentUrl" id="_currentUrl" value="{{ url()->current() }}">

    </div>

</div>
@endsection