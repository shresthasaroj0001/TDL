@extends('admin.master')
@section('btitle')
<title>Test</title>
@endsection
@section('mycss')
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css">

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
        width: 50px;
        /* Adjust based on your needs */
    }

    body {
        scroll-padding-top: 3rem;
        /* Match the top offset of your sticky element */
    }

    .modal-dialog.modal-fullscreen-sm-down {
        max-width: 1000px !important;
        /* Override */
        /* margin: 10px !important;  */
        /* Override */
    }

    tr.dtrg-group {
        text-align: right;
        background-color: aquamarine !important;
    }

    .card.mb-4 {
        border: none !important;
    }
</style>
@endsection

@section('myscript')

<script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.colVis.min.js"></script>

<script>
    $(function () {
        $("#mytable").DataTable({
            responsive: true,
            paging: false,
            lengthChange: false,
            
            layout: {
                topStart: {
                    buttons: [
                    'colvis',
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'copy', 
                    {
                        extend:'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, 
                    'pdf'
                    ]
                }
            }
        });

    });
</script>

@endsection

@section('bodycontent')
<div class="container-fluid" style="padding-left: 1px; padding-right:1px;">
    <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('entry-header.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">Next</li>
    </ol>
    @include('admin.messages')
    <div class="mb-4 sticky-top" style="position: -webkit-sticky; top: 3rem !important; z-index: 1000 !important;">
        <div class="card mb-4" style="background-color: #e9ecef; color: #6c757d;">
            <div class="card-body" style="padding-bottom: 0% !important">
                <div class="row headers" style="color: black">
                    <div class="col-md-3 col-sm-3 col" style="padding-left: 2px">
                        <p>Items: <b>{{$totalItem}}</b></p>
                    </div>
                    <div class="col-md-3 col-sm-3 col">
                        <p>Total: ${{$subTotal}}</p>
                    </div>
                    <div class="col-md-3 col-sm-3 col">
                        <p>HST: ${{$HSTTotal}}</p>
                    </div>
                    <div class="col-md-3 col-sm-3 col" style="padding-right: 2px; text-align: -webkit-right;">
                        <a href="{{ route('entry-header.done',[$entry_id]) }}">
                            <button type="button" id="previewBtn" class="btn btn-success btn-sm">Mark Done</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body" style="padding: 5px !important">
            <div class="row">
                <div class="table-responsive">
                    <table id="mytable" class="table responsive-table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($itemlist as $index=>$item)
                            <tr>
                                <td>{{$item->NAME}}</td>
                                <td>{{$item->category_name}}</td>
                                <td class="qty">{{$item->quantity}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_currentUrl" id="_currentUrl" value="{{ url()->current() }}">
@endsection