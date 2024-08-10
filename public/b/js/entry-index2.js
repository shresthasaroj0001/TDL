$(function () {
    toastr.options = {
        closeButton: false,
        debug: false,
        newestOnTop: false,
        progressBar: false,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

    var orderToCartQuantityTotal = 0;
    var totalprice = 0;
    var hstprice = 0;

    let urls = $("#_currentUrl").val();
    let urlArray = Array.from(urls);
    // urlArray.pop();
    let newUrl = urlArray.join("");

    var table = $("#mytable").DataTable({
        responsive: true,
        paging: false,
        lengthChange: false,
        fixedHeader: {
            header: true,
            footer: true,
            headerOffset: $('.sticky-top').outerHeight() + 40
        },
        layout: {
            topStart: {
                buttons: [
                    'colvis'
                ]
            }
        },

        rowCallback: function (row, data, displayNum, displayIndex, dataIndex) {
            let quantity = parseFloat(data[4]);
            let price = parseFloat(data[2]);
            let HST = parseInt(data[6]); // 1 or 0
            if (quantity > 0) orderToCartQuantityTotal++;

            let hstOnly = 0;
            if (HST == 1) hstOnly = 0.13 * price * quantity;
            else hstOnly = 0;
            totalprice += price * quantity + hstOnly;
            hstprice += hstOnly;
            //  console.log("rowcallback")
        },
        createdRow: function (row, data, index) {
            $(row).removeClass("highlight hover active"); // Remove the classes
            //categorySelected
            $(row).addClass(data[4]);
            //  console.log("createdRow")
            // console.log(data);
        },
        initComplete: function (settings, json) {
            console.log("DataTable initialized");
            console.log("Total item is " + orderToCartQuantityTotal);
            $("#NumberOfItems").html(orderToCartQuantityTotal);
            $("#totalPrice").html(totalprice.toFixed(2));
            $("#hstprice").html(hstprice.toFixed(2));
            // Additional code here
        },
        // createdRow: function (nRow, aData, iDataIndex) {
        // // Assuming you want to insert an input field into the first column
        // $('td:eq(3)', nRow).html('<input type="number" value="'+ aData[4]+ '" />');
        // // console.log(aData);
        // },
        "order": [3,'desc'],
        "ordering": true,
        columnDefs: [
            {
                // { className: "my_class", "targets": [ 4,5 ] }
                // targets: [2, 4, 5],
                // orderable: false, //price , quantity
            },
            { searchable: false, targets: [1, 2, 3, 4, 5] },
            // { width: "5%", targets: 4 },
            { targets: "hiddenCols", visible: false },
            { targets: "RestrictOrdering", orderable: false },
        ],
    });

    $(".minus").click(function () {
        var $input = $(this).parent().find("input");
        var count = parseInt($input.val()) - 1;
        count = count < 0 ? 0 : count;
        $input.val(count);
        $input.blur();
        return false;
    });

    $(".plus").click(function () {
        var $input = $(this).parent().find("input");
        $input.val(parseInt($input.val()) + 1);
        $input.blur();
        return false;
    });

    // table.on('draw.dt', function () {
    //     console.log('Table has been drawn');
    // });

    $(".editable-input").on("focus", function () {
        var $this = $(this);
        $this.select();
    });

    $(".editable-input").on("blur change input", function () {
        // Remove non-numeric characters
        var numericValue = this.value.replace(/[^0-9\.]/g, "");

        // Check if the length of the numeric value is greater than 15
        if (numericValue.length > 1) {
            // Prevent further input or revert the last character added
            // Option 1: Prevent further input
            // Option 2: Revert the last character added
            // This example shows how to revert the last character added
            this.value = numericValue.slice(0, -1);
            toastr["error"]("Input cannot exceed 15 characters");
        } else {
            // Update the input value if it's valid
            this.value = numericValue;
        }
    });

    $("#mytable").on("click", ".faa-plus", function () {});

    $(".editable-input").on("blur", function () {
        inputFieldValue = null;
        inputFieldValue = $(this);

        let currentTR = inputFieldValue.closest("tr");
        let rowData = table.row(currentTR).data();

        //console.log(rowData);

        // old data
        let quantity = parseFloat(rowData[4]);
        console.log("Old Quantity = " + quantity);
        let price = parseFloat(rowData[2]);
        let HST = parseInt(rowData[6]); // 1 or 0
        let entry_item_id = parseInt(rowData[7]);
        let category_list_id = parseInt(rowData[8]);
        newQuantity = parseFloat(inputFieldValue.val());
        if (quantity == newQuantity) return;

        let oldTotal = 0;
        let hstOnly = 0;
        if (HST == 1) hstOnly = 0.13 * price * quantity;
        else hstOnly = 0;
        oldTotal = price * quantity + hstOnly;

        totalprice -= oldTotal;
        hstprice -= hstOnly;

        if (quantity > 0) orderToCartQuantityTotal--;
        //new data

        hstOnly = 0;
        if (HST == 1) hstOnly = 0.13 * price * newQuantity;
        else hstOnly = 0;
        totalprice += price * newQuantity + hstOnly;
        hstprice += hstOnly;

        if (newQuantity > 0) {
            orderToCartQuantityTotal++;
            currentTR.addClass("categorySelected");
        } else {
            rowData[7] = 0; // removing the entry_item_id
            currentTR.removeClass("categorySelected");
        }
        // And you can write in this cell with this code
        rowData[4] = inputFieldValue.val();
        console.log("NewVal " + rowData[4]);

        $("#NumberOfItems").html(orderToCartQuantityTotal);
        $("#totalPrice").html(totalprice.toFixed(2));
        $("#hstprice").html(hstprice.toFixed(2));

        //ajax call
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
            },
            url: newUrl,
            type: "POST",
            data: {
                total_price: totalprice.toFixed(2),
                hst_price: hstprice.toFixed(2),
                new_quantity: newQuantity,
                entry_item_id: entry_item_id,
                price: price,
                category_list_id: category_list_id,
                cart_total_items: orderToCartQuantityTotal,
            },
            success: function (ddata) {
                console.log("AJAX Response" + ddata);

                if (Number(ddata) == NaN) {
                    alert("fail");
                    return;
                }

                let entry_item_id = parseInt(ddata);
                if (entry_item_id > 0) {
                    rowData[7] = entry_item_id;
                    $("#toastrOptions").text(
                        toastr["success"](rowData[0] + " Added to list")
                    );
                } else if (entry_item_id == 0)
                    $("#toastrOptions").text(
                        toastr["info"](rowData[0] + " updated")
                    );
                else $("#toastrOptions").text(toastr["error"]("Error"));
            },
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            fail: function (ddata) {
                console.log(ddata);
                alert("Error while processing your request");
            },
        });

        // Redraw the table
    });

    // table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
    // var data = this.data();

    // var stillUtc = moment.utc(data[6]).toDate();
    // var local = moment(stillUtc).local().format('YYYY-MM-DD hh:mm A');
    // data[6] = local;

    // this.row(rowIdx).data(data);
    // table.draw();
    // } );

    $("#btnsearch").click(function (e) {
        e.preventDefault();
        var url = $("#urls").val();
        var period = $("#periodId").val();
        if (period == "" || period == " ") {
            period = 0;
        }

        var category_id = $("#categoryId").val();
        if (category_id == "" || category_id == " ") {
            category_id = 0;
        }

        var category_list_id = $("#categoryListId").val();
        if (category_list_id == "" || category_list_id == " ") {
            category_list_id = 0;
        }

        var params = {
            period: period,
            category_id: category_id,
            category_list_id: category_list_id,
        };
        var new_url = url + "?" + jQuery.param(params);

        //console.log(new_url);
        location.href = new_url;
    });

    var collapsedGroups = {};
    var top = "";
    var parent = "";
    var previewTable = null;
    $(".headers").on("click", "#previewBtn", function () {
        // $("#mytbl .action-delete").click(function () {
        button = null;
        button = $(this);
        //(button.attr('rowid'));
        if (previewTable != null) {
            previewTable.destroy();
        }

        console.log($.fn.dataTable.version);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
            },
            url: newUrl + "/preview",
            type: "POST",
            success: function (ddata) {
                console.log(ddata);

                if (ddata && ddata != []) {
                    previewTable = $("#previewtable").DataTable({
                        paging: false,
                        lengthChange: false,
                        data: ddata,
                        searching: false,
                        info: false,
                        ordering: false,
                        columns: [
                            {
                                data: "NAME",
                                render: function (data, type, row) {
                                    let returnVal = data;
                                    if (row.hst_enforced == 1)
                                        returnVal +=
                                            '<span class="hstEnforced">*</span>';
                                    return returnVal;
                                },
                            },
                            { data: "category_name" },
                            {
                                data: "rate",
                                render: function (data, type, row) {
                                    return "$" + parseFloat(data);
                                },
                            },
                            {
                                data: "quantity",
                                render: function (data, type, row) {
                                    return parseFloat(data);
                                },
                            },
                            {
                                data: "subTotal",
                                render: function (data, type, row) {
                                    return "$" + parseFloat(data);
                                },
                            },
                            {
                                data: "hst_calculated",
                                render: function (data, type, row) {
                                    return "$" + parseFloat(data);
                                },
                            },
                        ],
                        order: [[1, 'asc']],
                        rowGroup: {
                            dataSrc: "category_name",
                            startRender : null,
                            endRender: function (rows, group) {
                                var quantitySum = 0;
                                var SubTotalSum = 0;
                                var HSTSum = 0;

                                rows.every(function (
                                    rowIdx,
                                    tableLoop,
                                    rowLoop
                                ) {
                                    HSTSum += parseFloat(this.data()['hst_calculated']);
                                    SubTotalSum += parseFloat(this.data()['subTotal']);
                                    quantitySum += parseFloat(this.data()['quantity']);
                                });

                                return $("<tr/>")
                                    .append(
                                        '<td colspan="2">' +
                                            group +
                                            "</td>"
                                    )
                                    .append("<td>" + quantitySum + "</td>")
                                    .append("<td>$" + SubTotalSum.toFixed(2) + "</td>")
                                    .append("<td>$" + HSTSum.toFixed(2) + "</td>");
                            }
                        },
                        columnDefs: [
                            { targets: [1], visible: false, searchable: false },
                        ],
                    });

                    $("#modal-entry").modal("show");
                    $("#previewTotal").html(
                        "Total $" + $("#totalPrice").html()
                    );
                }
            },
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            fail: function (ddata) {
                console.log(ddata);
                alert("Error while processing your request");
            },
        });
    });
});
