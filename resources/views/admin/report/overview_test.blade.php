@extends('admin.master')
@section('mycss')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    tr.odd td:first-child,
    tr.even td:first-child {
        padding-left: 4em;
    }
</style>
@endsection

@section('myscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>
<script>
$(function() {

var collapsedGroups = {};
var top = '';
var parent = '';

var hstsum = 0;
totalsum = 0;

var table = new DataTable('#mytable', {
    paging: false,
    searching: false,
    bInfo: false,

    "rowCallback": function(row, data) {
        var stillUtc = moment.utc(data[5]).toDate();
        var local = moment(stillUtc).local().format('YYYY-MM-DD hh:mm A');
        data[5] = local;
        $('td:eq(5)', row).html(local);

        hstsum += parseFloat(data[3]);
        totalsum += parseFloat(data[4]);

        $(row).attr('title', 'Ref: ' + data[6] + ' Notes: ' + data[7])
    },

    order: [
        [0, 'asc'],
        [1, 'desc'],
        [2, 'asc']
    ],

    rowGroup: {
        dataSrc: [0],
        startRender: function(rows, group, level) {
            // console.log(group + ' (' + rows.count() + ' rows)');

            var usageAvg = rows
                .data()
                .pluck(3)
                .reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            var percentageAvg = rows
                .data()
                .pluck(4)
                .reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            var all;

            if (level === 0) {
                top = group;
                all = group;
            } else {
                // if parent collapsed, nothing to do
                if (!!collapsedGroups[top]) {
                    console.log("I am returned")
                    return;
                }
                all = top + group;
            }

            var collapsed = !collapsedGroups[all];
            // console.log('collapsed:', collapsed);

            rowIndex = 0;
            rows.nodes().each(function(r) {
                r.style.display = collapsed ? 'none' : '';
            });

            //var ID = $( rows.nodes()[0] ).find('td').eq(2).data ("id");
            //find column value of group
            return $('<tr/>')
                .append('<td><b>' + group + ' (' + rows.count() + ')</b></td>' +
                    '<td></td><td></td>' +
                    '<td><b>$' + usageAvg + '</b></td>' +
                    '<td><b>$' + percentageAvg + '</b></td><td></td><td></td><td></td><td></td>')
                .attr('data-name', all).toggleClass('collapsed', collapsed);
        }
    },
    columnDefs: [{
            targets: [6, 7],
            visible: false
        },
        {
            targets: "_all",
            orderable: false
        }
    ]
});

$('#hstcol').html('$' + hstsum);
$('#totalcol').html('$' + totalsum);

$('#mytable tbody').on('click', 'tr.dtrg-start', function() {
    var name = $(this).data('name');
    collapsedGroups[name] = !collapsedGroups[name];
    table.draw(false);
});

$("#btnsearch").click(function(e) {

    e.preventDefault();
    var url = $('#urls').val();
    var period = $('#periodId').val();
    if (period == '' || period == ' ') {
        period = 0;
    }

    var category_id = $('#categoryId').val();
    if (category_id == '' || category_id == ' ') {
        category_id = 0;
    }

    var category_list_id = $('#categoryListId').val();
    if (category_list_id == '' || category_list_id == ' ') {
        category_list_id = 0;
    }

    var params = {
        'period': period,
        'category_id': category_id,
        'category_list_id': category_list_id
    };
    var new_url = url + "?" + jQuery.param(params);

    //console.log(new_url);
    location.href = new_url;

});


$("#mytable").on("click", ".action-delete", function() {
    // $("#mytbl .action-delete").click(function () {
    button = null;
    button = $(this);
    //(button.attr('rowid'));

    var result = confirm("Are You Sure You want to delete ?");
    if (result) {
        var i = $('#urls').val() + "/" + button.attr("rowid");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
            },
            url: i,
            type: "Delete",
            success: function(ddata) {
                if (ddata == 0) {
                    alert("Internal Error");
                    return false;
                }

                if (ddata == 1) {
                    let currentTR = button.closest("tr");
                    currentTR.addClass("Row4Delete");
                    if (currentTR.hasClass("child")) {
                        prevTR = currentTR.prev();
                        prevTR.addClass("Row4Delete");
                    }

                    $(".Row4Delete").remove();
                }
            },
        });
    }
});

});
</script>
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
                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="col-form-label">{{$categoryName}} Category:
                            {{$categoryId}}</label>
                        <div class="col-sm-12">
                            <select class="select2" name="categoryId" id="categoryId" data-placeholder=""
                                style="width: 100%;">
                                <option value="0">All</option>
                                @foreach ($categories as $item)
                                <option value="{{$item->id}}" @if ($categoryId==$item->id)
                                    selected="selected"@endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="col-form-label">Heading: {{$categoryListId}}</label>
                        <div class="col-sm-12">
                            <select class="select2" name="categoryListId" id="categoryListId" data-placeholder=""
                                style="width: 100%;">
                                <option value="0">All</option>
                                @foreach ($categoryLists as $item)
                                <option value="{{$item->id}}" @if ($categoryListId==$item->id)
                                    selected="selected"@endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="">
                    <button class="btn btn-primary" style="" id="btnsearch">Search</button>
                </div>
            </form>
        </div>
        <br>
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <i class="fas fa-table me-1"></i>List of {{$categoryName}}
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{route('entry.item.create',[$typeId])}}"><button class="btn btn-primary">Add New
                            {{$categoryName}}</button></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="mytable" class="display">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Period</th>
                        <th>Heading</th>
                        <th>HST</th>
                        <th>Total</th>
                        <th>Created At</th>
                        <th>Ref </th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index=>$item)
                    <tr>
                        <td>{{$item->catName}}</td>
                        <td>{{$item->periodName}}</td>
                        <td>{{$item->catlist}}</td>
                        <td>{{$item->hst_amt}}</td>
                        <td>{{$item->total_amt}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->ref_no}}</td>
                        <td>{{$item->description}}</td>
                        <td><button class="btn btn-danger action-delete" rowid="{{$item->entry_id}}"><i
                                    class="fa fa-trash" aria-hidden="true"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th id="hstcol">HST</th>
                        <th id="totalcol">Total</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<input type="hidden" name="_token" id="tokken" value="{{ csrf_token() }}">
<input type="hidden" name="_url" id="urls" value="{{route('entry.item.index',[$typeId])}}">
@endsection