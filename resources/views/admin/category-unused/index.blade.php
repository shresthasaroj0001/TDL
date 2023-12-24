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
    {{-- <h1 class="mt-4">Item Category</h1> --}}
    <br>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"> <i class="fas fa-table me-1"></i>
                    List of Category</div>
                <div class="col-md-6" style="text-align: right;">  
                    <a href="{{route('item-category.create')}}"><button class="btn btn-primary">Add New</button></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Age</th>
                        <th>Start date</th>
                        <th>Salary</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->description}}</td>
                        <td><a href="{{route('item-category.destroy',[$item->category_id])}}">
                            <button class="btn btn-danger">Delete</button></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection