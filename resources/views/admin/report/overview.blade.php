@extends('admin.master')
@section('mycss')


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

<style>
    caption {
        caption-side: top !important;
    }
</style>
@endsection

@section('myscript')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="/b/js/overview.js"></script>
@endsection

@section('bodycontent')
<div class="container-fluid px-4">
    <br>
    {{-- <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('entry_list') }}">Entry List</a></li>
        <li class="breadcrumb-item active">{{$categoryName}} List</li>
    </ol> --}}
    @include('admin.messages')
    <div class="card mb-4">

        <div class="card card-info">
            <form class="form-horizontal">
                <div class="card-body row">
                    <div class="col-md-4 form-group">
                        <label for="inputEmail3" class="col-form-label">Billing Period: {{$periodId}}</label>
                        <div class="col-sm-12">
                            <select class="select2" name="periodId" id="periodId" data-placeholder=""
                                style="width: 100%;">
                                <option value="0">All Periods</option>
                                @foreach ($periods as $item)
                                <option value="{{$item->id}}" @if ($periodId==$item->id)
                                    selected="selected"@endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="">
                    <button class="btn btn-primary" style="" id="btnsearch">Search</button>
                    {{-- <a href="{{route('entry.item.create',[$typeId])}}" target="_blank"><button class="btn btn-primary">Add New {{$categoryName}}</button></a> --}}
                </div>
            </form>
        </div>

        @if(count($NotInList) > 0)
        <br>
        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header alert-secondary" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" data-target="#collapseOne" aria-expanded="true"
                        aria-controls="collapseOne">
                        Missing Monthly {{$categoryName}}
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#accordionExample" data-parent="#accordionExample">
                    <div class="accordion-body">
                        <table id="tblmonthly" class="table display">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Category</th>
                                    <th>Heading</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($NotInList as $index=>$item)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>{{$item->category_name}}</td>
                                    <td>{{$item->category_list_name}}</td>
                                    <td><button class="btn btn-primary action-update" rowid="{{$item->id}}"><i
                                                class="fa fa-pencil" aria-hidden="true"></i> Update</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card-header">
            <div class="row">
                <div class="alert alert-secondary" style="margin: 0" role="alert">
                    List of {{$categoryName}} <b>HST:</b> ${{$hst_sum}} <b>Total: </b>${{$total_sum}}
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            @foreach ($list as $indexx=>$item)

            <table id="" class="table table-hover display ">
                <caption>List of {{$list[$indexx]['category']}}</caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Period</th>
                        <th scope="col">Heading</th>
                        <th scope="col">Reference</th>
                        <th scope="col">HST</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list[$indexx]['data'] as $index=>$entry)
                    <tr>
                        <th scope="row">{{$index + 1}}</th>
                        <td>{{$entry->periodName}}</td>
                        <td>{{$entry->catlist}}</td>
                        <td>{{$entry->ref_no}}</td>
                        <td>${{$entry->hst_amt}}</td>
                        <td>${{$entry->total_amt}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-group-divider table-light">
                    <tr style="font-weight: bold">
                        <td colspan="4" style="text-align: right">Total</td>
                        <td>${{$list[$indexx]['hst_amt']}}</td>
                        <td>${{$list[$indexx]['total_amt']}}</td>
                    </tr>
                </tfoot>
            </table>
            @endforeach
        </div>

        <div class="modal" id="modal-entry" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content" id="modalsss">
                    <div class="modal-header">
                        <h4 class="modal-title">Register a entry</h4>
                        {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                    </div>
                    <div class="modal-body">
                        <h3 id="h3Title"></h3>
                        <form action="" class="form-horizontal">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Period:</label>
                                    <div class="col-sm-12">
                                        <p id="previousPeriod">Dec-2222</p>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">HST Amt:</label>
                                    <div class="col-sm-12">
                                        <p id="previousHstAmt">Dec-2222</p>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Total Amt:</label>
                                    <div class="col-sm-12">
                                        <p id="previousTotalAmt">Dec-2222</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Period:</label>
                                    <div class="col-sm-12">
                                        <p id="proposedPeriod">Dec-2222</p>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">HST Amt:</label>
                                    <div class="row" style="width: 100%">
                                        <input type="number" name="" id="proposedHstAmt">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Total Amt:</label>
                                    <div class="row" style="width: 100%">
                                        <input type="number" name="" id="proposedTotalAmt">
                                    </div>
                                </div>
                            </div>

                        </form>
                        <b>Note: </b> Display Changes May Change On Refreshing Page.
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

        {{-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Register a entry</h5>
                         
                    </div>
                    <div class="modal-body">
                        <form action="" class="form-horizontal">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Period:</label>
                                    <div class="col-sm-12">
                                        <p id="previousPeriod">Dec-2222</p>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">HST Amt:</label>
                                    <div class="col-sm-12">
                                        <p id="previousHstAmt">Dec-2222</p>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Total Amt:</label>
                                    <div class="col-sm-12">
                                        <p id="previousTotalAmt">Dec-2222</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Period:</label>
                                    <div class="col-sm-12">
                                        <p id="proposedPeriod">Dec-2222</p>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">HST Amt:</label>
                                    <div class="row" style="width: 100%">
                                        <input type="text" name="" id="proposedHstAmt">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail3" class="col-form-label">Total Amt:</label>
                                    <div class="row" style="width: 100%">
                                        <input type="text" name="" id="proposedTotalAmt">
                                    </div>
                                </div>
                            </div>

                        </form>
                        <b>Note: </b> Display Changes May Change On Refreshing Page.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" id="closeModalBtn"
                            data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="UpdateModalBtn">Update Now</button>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</div>
<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_url" id="urls" value="{{route('report_overview',[$typeId])}}">
@endsection