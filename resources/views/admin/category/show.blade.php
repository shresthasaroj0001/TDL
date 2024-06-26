@extends('admin.master')
@section('mycss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endsection

@section('myscript')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
    <script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
    <script src="/b/js/show.js"></script>
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
                            <button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modal-column" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modalsss">
                <div class="modal-header">
                    <h4 class="modal-title">Register a entry</h4>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="reportcolumntable" class="table table-hover display ">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categorylist as $index=>$entry)
                                <tr>
                                    <td>{{$entry->name}}</td>
                                    <td>{{$entry->description}}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-warning" id="closeModalBtn"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="UpdateModalBtn">Update Now</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</div>
@endsection