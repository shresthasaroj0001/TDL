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
@section('bodycontent')
<div class="container-fluid px-4">
    {{-- <h1 class="mt-4">Item Category</h1> --}}
    <br>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('entry-header.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6"> <i class="fa fa-calendar" aria-hidden="true"></i> Last Order:
                            {{$lastOrder}}</div>
                        <div class="col-md-6" style="text-align: right;">
                            <a href="{{ route('entry-header.index') }}"><button type="button" class="btn btn-info">View
                                    All
                                    Orders</button></a>
                        </div>
                    </div>
                </div>

                <div class="card card-info">
                    <form class="form-horizontal" method="POST" action="{{ route('entry-header.store')}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Date</label>
                                        <div id="select_date"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" id="savebtn" class="btn btn-primary savebtn">Create</button>
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

@section('myscript')
{{-- <script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script> --}}
<!-- Include jQuery UI CSS -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- Include jQuery UI JS -->
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(function(){
        $( "#select_date" ).datepicker({ minDate: 0, maxDate: "+1M +10D" });
        // $("#select_date").focus();
});
</script>
@endsection