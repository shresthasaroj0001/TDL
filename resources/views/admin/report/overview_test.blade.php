@extends('admin.master')
@section('mycss')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endsection

@section('myscript')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script>
    $(function(){

    var table = $("#mytable").DataTable();

    table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
    var data = this.data();

    var stillUtc = moment.utc(data[6]).toDate();
    var local = moment(stillUtc).local().format('YYYY-MM-DD hh:mm A');
    data[6] = local;
    
    this.row(rowIdx).data(data);
    table.draw();
    } );   

    //console.log(moment(new Date()).format('YYYY-MM-DD hh:mm A'));

    // $(".select2").select2({
    // theme :'classic'
    // });

    $("#btnsearch").click(function(e){

    e.preventDefault();
    var url = $('#urls').val();
    var period = $('#periodId').val();
    if(period == '' || period == ' '){
        period = 0;
    }

    var category_id = $('#categoryId').val();
    if(category_id == '' || category_id == ' '){
        category_id = 0;
    }

    var category_list_id = $('#categoryListId').val();
    if(category_list_id == '' || category_list_id == ' '){
        category_list_id = 0;
    }

    var params = { 'period':period, 'category_id':category_id, 'category_list_id':category_list_id };
    var new_url = url+"?" + jQuery.param(params);

    //console.log(new_url);
    location.href = new_url;

    });


    $("#mytable").on("click", ".action-delete", function () {
        // $("#mytbl .action-delete").click(function () {
        button = null;
        button = $(this);
        //(button.attr('rowid'));

        var result = confirm("Are You Sure You want to delete ?");
        if (result) {
            var i = $('#urls').val() + "/" + button.attr("rowid");
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
                    }
                },
            });
        }
    });

    });
</script>
@endsection

@section('bodycontent')
<div class="container-fluid px-4">
    <br>
    {{-- <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>   
        <li class="breadcrumb-item"><a href="{{ route('entry_list') }}">Entry List</a></li>
        <li class="breadcrumb-item active">{{$categoryName}} List</li>
    </ol> --}}
    @include('admin.messages')
    <div class="card mb-4">

        <div class="card card-info">
            <form class="form-horizontal">
                <div class="card-body row">
                    <div class="col-md-4 form-group">
                        <label for="inputEmail3" class="col-form-label">Billing Period: {{$periodId}}</label>
                        <div class="col-sm-12">
                            <select class="select2" name="periodId" id="periodId" data-placeholder=""
                                style="width: 100%;">
                                <option value="0">All Periods</option>
                                @foreach ($periods as $item)
                                <option value="{{$item->id}}" @if ($periodId==$item->id) selected="selected"@endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="col-form-label">{{$categoryName}} Category:
                            {{$categoryId}}</label>
                        <div class="col-sm-12">
                            <select class="select2" name="categoryId" id="categoryId" data-placeholder="" style="width: 100%;">
                                <option value="0">All</option>
                                @foreach ($categories as $item)
                                <option value="{{$item->id}}" @if ($categoryId==$item->id) selected="selected"@endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="col-form-label">Heading: {{$categoryListId}}</label>
                        <div class="col-sm-12">
                            <select class="select2" name="categoryListId" id="categoryListId" data-placeholder=""
                                style="width: 100%;">
                                <option value="0">All</option>
                                @foreach ($categoryLists as $item)
                                <option value="{{$item->id}}" @if ($categoryListId==$item->id) selected="selected"@endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="">
                    <button class="btn btn-primary" style="" id="btnsearch">Search</button>
                </div>
            </form>
        </div>
        <br>
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <i class="fas fa-table me-1"></i>List of {{$categoryName}}  <b>HST:</b> ${{$hst_sum}} <b>Total:</b> ${{$total_sum}}
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{route('entry.item.create',[$typeId])}}"><button class="btn btn-primary">Add New {{$categoryName}}</button></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="mytable"  class="display">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Period</th>
                        <th>Category</th>
                        <th>Heading</th>
                        <th>HST</th>
                        <th>Total</th>
                        <th>Created At</th>
                        <th>Ref </th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$item->periodName}}</td>
                        <td>{{$item->catName}}</td>
                        <td>{{$item->catlist}}</td>
                        <td>{{$item->hst_amt}}</td>
                        <td>{{$item->total_amt}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->ref_no}}</td>
                        <td>{{$item->description}}</td>
                        <td><button class="btn btn-danger action-delete" rowid="{{$item->entry_id}}"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_url" id="urls" value="{{route('entry.item.index',[$typeId])}}">
@endsection