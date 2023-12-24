@extends('admin.master')
@section('mycss')
{{--
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" /> --}}
<link rel="stylesheet" href="/b/css/select2.min.css">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: blue !important;
    }
</style>
@endsection

@section('myscript')

<script src="/b/js/datatables-simple-demo.js"></script>
<script src="/b/js/select2.min.js"></script>
<script>
    $(function(){
    $('.select2').select2();

    $("#savebtn").click(function(){
        if($('#title').val() == '' || $('#title').val() == ' '){
            alert('Please Write Title');
            return;
        }

        var content = tinyMCE.get('description-textarea').getContent(), patt;

            //Here goes the RegEx
            patt = /^<p>(&nbsp;\s)+(&nbsp;)+<\/p>$/g;

            if (content == '' || patt.test(content)) {
                $('.bgcolor').css("border", "1px solid Red")
                alert('Please Write descriptions');
            return;
            }
            else {
                $('.bgcolor').removeAttr("style")
            } //('.bgcolor') is nothing but a div around the 'tinyeditor' to have the red border when validation occurs.

        // var editorContent = tinyMCE.get('tinyeditor').getContent();
        // if (editorContent == '')
        // {
        //     alert('Please Write descriptions');
        //     return;
        // }
       
        if($('#status').val() == '' || $('#status').val() == ' '){
            alert('Please select status');
            return;
        }
        // if($('#status').val() == '' || $('#status').val() == ' '){
        //     alert('Please Write Order No');
        //     return;
        // }
            $('#savebtn').attr("disabled",true);
        $('.form-horizontal').submit();

    });
});
</script>
@endsection

@section('bodycontent')
<div class="container-fluid px-4">
    {{-- <h1 class="mt-4">Item Category</h1> --}}
    <br>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('item-category.index') }}">Category</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href="{{ route('item-category.index') }}"><button type="button" class="btn btn-info">View All
                                Category</button></a>
                    </div>
                </div>

                <div class="card card-info">
                    <form class="form-horizontal" method="POST" action="{{ route('item-category.store')}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

                        <div class="card-body">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Title <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" autocomplete="off" required class="form-control"
                                        value="{{ old('title') }}" id="title" placeholder="Title" name="title">
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
                            <br>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">PLU <span
                                        style="color: red">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" autocomplete="off" required class="form-control"
                                        value="{{ old('plu') }}" id="plu" placeholder="PLU" name="plu">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" required id="stats" name="stats">
                                            <option value="1" @if (old('stats')==1 || old('stats')==' ') selected="selected" @endif
                                                class="form-control">Active</option>
                                            <option value="0" @if (old('stats')==0) selected="selected" @endif
                                                class="form-control">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                sdf{{old('stats')}}dfs
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Select Categories</label>
                                        <select class="select2" class="form-control" name="blogcategory[]"
                                            multiple="multiple" data-placeholder="Select category" style="width: 100%;">
                                            <option value="2">Box</option>
                                            <option value="3">Sleeve</option>
                                            <option value="1">Case</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group row">
                                <div class="col-md-6">
                                    
                                    <div class="form-group">
                                        <label for="status">Date</label>
                                            <input type="text" name="dates" id="" class="form-control">
                                    </div>
                                </div>
                            </div> -->
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