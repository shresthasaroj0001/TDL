@extends('admin.master')

@section('bodycontent')
<div class="container-fluid px-4">
    <br>
    <ol class="breadcrumb mb-4">
        {{-- <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('entry_list') }}">Entry List</a></li> --}}
        <li class="breadcrumb-item"> Orders
        </li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
    @include('admin.messages')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools" style="text-align: center">
                        You have a pending order. Please make a selection
                        {{-- <a href="{{ route('entry.item.index',[$categoryId]) }}"><button type="button"
                                class="btn btn-info">View {{$categoryName}} List</button></a> --}}
                    </div>
                </div>

                <div class="card ">
                    <form class="form-horizontal" method="POST" action="{{ route('entry-header.create')}}">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

                        <div class="card-body" style="margin-bottom: 0%;">
                            <div class="row">
                                <div class="col">
                                    <label for="inputEmail3" class="col-form-label"><b>Location: </b></label>
                                    @if ($storeId == 1)
                                    <span>Danforth</span>
                                    @else
                                    <span>Markham</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="inputEmail3" class="col-form-label"><b>Order Date: </b></label>
                                    <span>{{$lastOrder}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="inputEmail3" class="col-form-label"><b>Items: </b></label>
                                    <span>{{$cart_items}}</span>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="col-form-label"><b>Total: </b></label>
                                    <span>${{$total_price}}</span>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="col-form-label"><b>HST: </b></label>
                                    <span>${{$hst_price}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col">
                                    <a href="{{ route('entry-header.delete',[$order_id]) }}"><button type="button"
                                            id="savebtn" class="btn btn-danger savebtn">Delete</button></a>
                                </div>
                                <div class="col" style="text-align: center">
                                    <a href="{{ route('entry.item.index',['id'=>$order_id,'store'=>$storeId]) }}"><button
                                            type="button" id="savebtn"
                                            class="btn btn-primary savebtn">Continue</button></a>
                                </div>
                                <div class="col" style="text-align: right">
                                    @if ($cart_items == 0)
                                    <button type="button" id="savebtn" disabled class="btn btn-success savebtn">Mark
                                        Done</button>
                                    @else
                                    <a href="{{ route('entry-header.done',[$order_id]) }}"><button type="button"
                                            id="savebtn" class="btn btn-success savebtn">Mark Done</button></a>
                                    @endif
                                </div>
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