@extends('admin.master')
@section('mycss')
<link rel="stylesheet" href="/b/css/select2.min.css">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: blue !important;
    }

    .select2-container .select2-choice {
        display: block !important;
        height: 36px !important;
        white-space: nowrap !important;
        line-height: 26px !important;
    }
</style>
@endsection

@section('myscript')
<script src="/b/js/select2.min.js"></script>
<script>
$(function(){
    $(".select2").select2({
    theme :'classic'
    });

    $("#savebtn").click(function(){
    
    if($('#name').val() == '' || $('#name').val() == ' '){
    alert('Please Write Period-Title');
    return;
    }
    
    $('.form-horizontal').submit();
    });
    });
</script>
@endsection

@section('bodycontent')
<div class="container-fluid px-4">
    <br>
    <ol class="breadcrumb mb-4">
        {{-- <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('setting_list') }}">Category List</a></li> --}}
        <li class="breadcrumb-item"><a href="{{ route('setting.list.index',[$categoryId]) }}">{{$categoryName}}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    @include('admin.messages')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href="{{ route('setting.list.index',[$categoryId]) }}"><button type="button"
                                class="btn btn-info">View {{$categoryName}} List</button></a>
                    </div>
                </div>

                <div class="card card-info">
                    <form class="form-horizontal" method="POST" action="{{ route('setting.list.update',[$categoryId, $item->category_list_id])}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">

                        <div class="card-body">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Name: <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" autocomplete="off" required class="form-control"
                                        value="@if(old('name')==""){{$item->name}}@else{{old('name')}}@endif" id="name" placeholder="" name="name">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="status" class="col-sm-2 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="select2" name="category_id" data-placeholder="" style="width: 100%;">
                                        @foreach ($categorylist as $opt)
                                            @if(old('category_id')=='')
                                                <option value="{{$opt->category_id}}" @if($item->category_id==$opt->category_id) selected="selected" @endif>{{$opt->name}}</option>
                                            @else
                                                <option value="{{$opt->category_id}}" @if(old('stats')==$opt->category_id) selected="selected" @endif>{{$opt->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="status" class="col-sm-2 col-form-label">HST Applicable ?</label>
                                <div class="col-sm-10">
                                    <select class="form-control" required id="hst_enforced" name="hst_enforced">
                                        @if(old('hst_enforced')=='')
                                            <option value="0" @if($item->hst_enforced==0) selected="selected" @endif>No</option>
                                            <option value="1" @if($item->hst_enforced==1) selected="selected" @endif>Yes</option>
                                        @else
                                            <option value="0" @if(old('hst_enforced')==0) selected="selected" @endif>No</option>
                                            <option value="1" @if(old('hst_enforced')==1) selected="selected" @endif>Yes</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Price: <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" autocomplete="off" required class="form-control"
                                        value="@if(old('price')==""){{$item->price}}@else{{old('price')}}@endif" id="price" placeholder="" name="price">
                                </div>
                            </div>  
                            <br>
                            <div class="form-group row">
                                <label for="body" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10 bgcolor">
                                    <textarea name="body" required class="form-control" id="description-textarea"
                                        cols="30">@if(old('body')==""){{$item->description}}@else{{old('body')}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" id="savebtn" class="btn btn-primary savebtn">Update</button>
                        </div>
                        <!-- /.card-footer -->
                    </form>
                </div>

            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection