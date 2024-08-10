@extends('admin.master')
@section('mycss')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
@endsection

@section('myscript')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script>
    $(function(){
    var table = $("#example").DataTable({
    });
    
    $("#example").on("click", ".action-delete", function () {
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
    }
    },
    });
    }
    });
    });
</script>
@endsection

@section('bodycontent')
<div class="container-fluid px-1">
    <ol class="breadcrumb mb-2 mt-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('setting_list') }}">Settings List</a></li>
        <li class="breadcrumb-item active"> List</li>
    </ol>
    @include('admin.messages')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                {{-- <div class="col-md-6"> <i class="fas fa-table me-1"></i>
                    List of {{$categoryName}}s</div> --}}
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{route('setting.list.create',[$categoryId])}}"><button class="btn btn-primary">Add New
                            {{$categoryName}}</button></a>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding: 0px;">
            <div class="table table-responsive">
                <table id="example" class="display" style="width:98%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>HST</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index=>$item)
                        <tr>
                            {{-- <td>{{$item->category_list_id}}</td> --}}
                            <td>{{$item->name}}</td>
                            {{-- <td>{{$item->description}}</td> --}}
                            <td>{{$item->category}}</td>
                            <td>
                                @if ($item->hst_enforced == 1)
                                Yes
                                @else
                                No
                                @endif
                            </td>
                            <td>${{$item->price}}</td>
                            <td>
                                <a href="{{route('setting.list.edit',[$categoryId, $item->category_list_id])}}">
                                    <button class="btn btn-primary"><i class="fa fa-pen"></i></button>
                                </a>
                               <button class="btn btn-danger action-delete" rowid="{{$item->category_list_id}}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_currentUrl" id="_currentUrl" value="{{ url()->current() }}">

@endsection