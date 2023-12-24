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
                    <a href="{{route('setting.name.create',[$typeid])}}"><button class="btn btn-primary">Add New</button></a>
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
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->description}}</td>
                        <td><button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            {{-- <td><a href="{{route('item-category.destroy',[$item->category_id])}}">
                                <button class="btn btn-danger">Delete</button></a></td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection