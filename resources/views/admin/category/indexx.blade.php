@extends('admin.master')
@section('mycss')
{{-- <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" /> --}}
@endsection

@section('myscript')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
crossorigin="anonymous"></script>
<script src="/b/js/datatables-simple-demo.js"></script>
@endsection

@section('bodycontent')
<div class="container-fluid px-4">
    <br>
    @include('admin.messages')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"> <i class="fas fa-table me-1"></i>
                    Manage Your Master Settings Names</div>
                <div class="col-md-6" style="text-align: right;">  
                    {{-- <a href="{{route('setting-period.create')}}"><button class="btn btn-primary">Add New</button></a> --}}
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Period Name</td>
                        <td>Month - Billing Period Name</td>
                        <td><a href="{{route('setting.name.index',[1])}}"><button class="btn btn-danger">Manage</button></a></td>
                    </tr>
                    <tr>
                        <td>Expense Category</td>
                        <td>Heading for Expenses like Bank, Utilities, Supplier Category</td>
                        <td><a href="{{route('setting.name.index',[2])}}"><button class="btn btn-danger">Manage</button></a></td>
                    </tr>
                    <tr>
                        <td>Income Category</td>
                        <td>In Store, Online devices name</td>
                        <td><a href="{{route('setting.name.index',[3])}}"><button class="btn btn-danger">Manage</button></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection