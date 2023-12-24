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
                    <form class="form-horizontal" method="POST" action="{{ route('setting.list.store',[$categoryId])}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

                        <div class="card-body">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Name: <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" autocomplete="off" required class="form-control"
                                        value="{{ old('name') }}" id="name" placeholder="" name="name">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="status" class="col-sm-2 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="select2" name="category_id" data-placeholder="" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach ($categorylist as $item)
                                        <option value="{{$item->category_id}}" @if (old('category_id')==$item->category_id) selected="selected" @endif>{{$item->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="status" class="col-sm-2 col-form-label">Is Monthly</label>
                                <div class="col-sm-10">
                                    <select class="form-control" required id="ismonthly" name="ismonthly">
                                        <option value="1" @if (old('ismonthly')== 1 || old('ismonthly')=='' )
                                            selected="selected" @endif class="form-control">Yes</option>
                                        <option value="0" @if (old('ismonthly')== 0) selected="selected" @endif
                                            class="form-control">No</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="body" class="col-sm-2 col-form-label">Description</label>
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