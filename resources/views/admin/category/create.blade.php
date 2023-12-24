@extends('admin.master')
@section('mycss')
 
@endsection

@section('myscript')
<script>
    $(function(){

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
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('setting_name') }}">Settings</a></li>
        <li class="breadcrumb-item"><a href="{{ route('setting.name.index',[$typeid]) }}">{{$setting_name}}</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
    @include('admin.messages')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href="{{ route('setting.name.index',[$typeid]) }}"><button type="button" class="btn btn-info">View All {{$setting_name}}</button></a>
                    </div>
                </div>

                <div class="card card-info">
                    <form class="form-horizontal" method="POST" action="{{ route('setting.name.store',[$typeid])}}">
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