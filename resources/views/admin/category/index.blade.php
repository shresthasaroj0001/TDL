@extends('admin.master')
@section('mycss')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endsection

@section('myscript')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script>
    $(function(){
    var table = $("#datatablesSimple").DataTable({
    });
    
    $("#datatablesSimple").on("click", ".action-delete", function () {
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
<div class="container-fluid px-4">
    <br>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('setting_name') }}">Settings</a></li>
        <li class="breadcrumb-item active">{{$setting_name}}</li>
    </ol>
    @include('admin.messages')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"> <i class="fas fa-table me-1"></i>
                    List of {{$setting_name}}</div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{route('setting.name.create',[$typeid])}}"><button class="btn btn-primary">Add
                            New</button></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table display">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->description}}</td>
                        <td>
                            <a href="{{route('setting.name.edit',[$typeid, $item->id])}}">
                                <button class="btn btn-primary"><i class="fa fa-pen"></i></button>
                            </a>
                            <button class="btn btn-danger action-delete" rowid="{{$item->id}}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_currentUrl" id="_currentUrl" value="{{ url()->current() }}">

@endsection