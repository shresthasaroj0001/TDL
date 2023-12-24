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
        <li class="breadcrumb-item"><a href="{{ route('entry_list') }}">Entry List</a></li> --}}
        <li class="breadcrumb-item"><a href="{{ route('entry.item.index',[$categoryId]) }}">{{$categoryName}} List</a>
        </li>
        <li class="breadcrumb-item active">Create</li>
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
                    <form class="form-horizontal" method="POST" action="{{ route('entry.item.store',[$categoryId])}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail3" class="col-form-label">Category: <span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-12">
                                        <select class="select2" name="category_list_id" data-placeholder="" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($list as $item)
                                            <option value="{{$item->id}}" @if (old('category_list_id')==$item->id) selected="selected" @endif>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmail3" class="col-form-label">Period Name: <span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-12">
                                        <select class="select2" name="period" data-placeholder="Select Billing Period" style="width: 100%;">
                                            @foreach ($periods as $item)
                                            <option value="{{$item->id}}" @if (old('period')==$item->id) selected="selected" @endif>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">H.S.T: <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="number" autocomplete="off" required class="form-control"
                                        value="{{ old('hst') }}" id="hst" placeholder="" name="hst">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Total Paid: <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="number" autocomplete="off" required class="form-control"
                                        value="{{ old('total') }}" id="total" placeholder="" name="total">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Reference No: <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" autocomplete="off" required class="form-control"
                                        value="{{ old('ref_no') }}" id="ref_no" placeholder="" name="ref_no">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="body" class="col-sm-2 col-form-label">Notes:</label>
                                <div class="col-sm-10 bgcolor">
                                    <textarea name="body" required class="form-control" id="description-textarea"
                                        cols="30">{{ old('body') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" id="savebtn" class="btn btn-primary savebtn">Save</button>
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