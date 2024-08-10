@extends('admin.master')

@section('bodycontent')
<div class="container-fluid px-4">
    <br>
    <ol class="breadcrumb mb-4">
        {{-- <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('entry_list') }}">Entry List</a></li> --}}
        <li class="breadcrumb-item"> Orders</li>
        @if ($operationId == 1)
            <li class="breadcrumb-item active">Create</li>            
        @else
            <li class="breadcrumb-item active">Update</li>
        @endif
    </ol>
    @include('admin.messages')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools" style="text-align: center">
                        Please make a store selection...
                    </div>
                </div>

                <div class="card ">
                    <form class="form-horizontal" method="POST" action="{{ route('entry-header.create')}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                @if ($operationId == 1)
                                    <div class="col">
                                        <a href="{{ route('entry-header.create',['store' => '1']) }}"><button type="button"
                                                id="" class="btn btn-primary ">Danforth</button></a>
                                    </div>
                                    <div class="col" style="text-align: right">
                                        <a href="{{ route('entry-header.create',['store' => '2']) }}"><button type="button"
                                                id="" class="btn btn-primary ">Markham</button></a>
                                    </div>
                                @else
                                    <div class="col">
                                        <a href="{{ route('entry.item.create',['store' => '1','id'=>0]) }}"><button type="button"
                                                id="" class="btn btn-primary ">Danforth</button></a>
                                    </div>
                                    <div class="col" style="text-align: right">
                                        <a href="{{ route('entry.item.create',['store' => '2','id'=>0]) }}"><button type="button"
                                                id="" class="btn btn-primary ">Markham</button></a>
                                    </div>
                                @endif
                            </div>
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