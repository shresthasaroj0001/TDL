@extends('admin.master')
@section('mycss')

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
<style>
    div.dt-container div.dt-layout-row {
        display: inline-table;
    }
</style>
@endsection

@section('myscript')
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
    $(function () {
        $("#example1").DataTable({
            "order": [4,'desc'],
            "ordering": true,
            columnDefs: [
                { targets: "hiddenCols", visible: false },
                { targets: "RestrictOrdering", orderable: false },
            ],
        });
    });
</script>
@endsection


@section('bodycontent')
<div class="container-fluid px-1">
    {{-- <h1 class="mt-4">Item Category</h1> --}}
    <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col"> <i class="fas fa-table me-1"></i>
                    List of Orders Placed</div>
                <div class="col" style="text-align: right;">
                    <a href="{{route('entry-header.create')}}"><button class="btn btn-primary"></button></a>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table id="example1" class="table no-wrap display" style="width: 100%">
                <thead>
                    <tr>
                        <th>Order Date</th>
                        <th>Store</th>
                        <th>Subtotal</th>
                        <th>HST</th>
                        <th class="hiddenCols">-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>
                            <a href="{{route('entry-header.edit',['id'=>$item->entry_id, 'store'=>$item->store_id])}}">
                                {{$item->entry_date}}</a>
                        </td>
                        <td>
                            @if ($item->store_id == 1)
                            Danforth
                            @elseif ($item->store_id == 2)
                            Markham
                            @else
                            -
                            @endif
                        </td>
                        <td>{{$item->total_price}}</td>
                        <td>{{$item->hst_price}}</td>
                        <td>{{$item->entry_id}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection