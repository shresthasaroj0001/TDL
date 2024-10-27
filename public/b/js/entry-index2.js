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
            headerOffset: $(".sticky-top").outerHeight() + 40,
        },
        layout: {
            topStart: {
                buttons: ["colvis"],
            },
        },
        order: [2, "desc"],
        ordering: true,
        columnDefs: [
            { targets: "hiddenCols", visible: false },
            { targets: "RestrictOrdering", orderable: false },
            { targets: [1, 2, 3, 4, 5], searchable: false },
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

    $(".editable-input").on("focus", function () {
        var $this = $(this);
        $this.select();
    });

    $(".editable-input").on("blur change input", function () {
        // Remove non-numeric characters
        var numericValue = this.value.replace(/[^0-9\.]/g, "");
        inputFieldValue = $(this);

        // Check if the length of the numeric value is greater than 15
        if (numericValue.length > 1) {
            // Prevent further input or revert the last character added
            // Option 1: Prevent further input
            // Option 2: Revert the last character added
            // This example shows how to revert the last character added
            this.value = parseInt(9); // numericValue.slice(0, -1);
            toastr["error"]("Input cannot exceed 15 characters");
        } else {
            // Update the input value if it's valid
            this.value = numericValue;
        }

        let currentValue = parseInt(this.value);

        let currentTR = inputFieldValue.closest("tr");
        let rowData = table.row(currentTR).data();
        let oldValue = parseInt(rowData[6]);
        if (currentValue != oldValue) {
            let rate = parseFloat(rowData[4]); //$(this).closest('tr').find('.rate-input').text());
            let isHst = parseFloat(rowData[3]); // $(this).closest('tr').find('.hst-input').text());

            let Amount = 0;
            let Hst = 0;
            Amount = parseFloat(rate * currentValue);
            if (isHst == 1) Hst = parseFloat(0.13 * Amount);

            rowData[9] = Amount.toFixed(2);
            rowData[10] = Hst.toFixed(2);
            rowData[6] = currentValue;
            // $(this).closest('tr').find('.subtotal-amount').text(Amount.toFixed(2));
            // $(this).closest('tr').find('.subtotal-hst').text(Hst.toFixed(2));
            // $(this).closest('tr').find('.quantity-input').text(currentValue);

            updateDatabase(inputFieldValue);
        }
    });

    function updateTotalToGrid() {
        orderToCartQuantityTotal = table
            .column(6)
            .data()
            .reduce((sum, value) => {
                return sum + parseFloat(value);
            }, 0);
        // console.log("Total item is " + orderToCartQuantityTotal);
        $("#NumberOfItems").html(orderToCartQuantityTotal);

        totalprice = table
            .column(9)
            .data()
            .reduce((sum, value) => {
                return sum + parseFloat(value);
            }, 0);
        $("#totalPrice").html(totalprice.toFixed(2));

        hstprice = table
            .column(10)
            .data()
            .reduce((sum, value) => {
                return sum + parseFloat(value);
            }, 0);
        $("#hstprice").html(hstprice.toFixed(2));
    }
    updateTotalToGrid();

    function updateDatabase(inputFieldValue) {
        // $(".editable-input").on("blur", function () {
        let isOrderPlaced = parseInt($("#_order_placed").val());
        if(isOrderPlaced == 1)
        {
            var result = confirm("Do you want to override save ?");
            if(!result)
                return;
        }

        $.blockUI();

        updateTotalToGrid();

        let currentTR = inputFieldValue.closest("tr");
        let rowData = table.row(currentTR).data();

        let entry_item_id = parseInt(rowData[7]);
        let category_list_id = parseInt(rowData[8]);
        let price = parseFloat(rowData[4]);
        console.log(price);
        let newQuantity = parseFloat(rowData[6]);

        if (newQuantity > 0) {
            currentTR.addClass("categorySelected");
        } else {
            rowData[7] = 0; // removing the entry_item_id
            currentTR.removeClass("categorySelected");
        }
        // And you can write in this cell with this code

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
                        toastr["success"](rowData[0] + " Added")
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
    }

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
        table2.ajax.reload();

        $("#modal-entry").modal("show");
        $("#previewTotal").html("Total $" +  (totalprice+hstprice).toFixed(2));
    });

    var table2 = $("#previewtable").DataTable({
        ajax: {
            url: newUrl + "/preview",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
            },
            data: function (d) {
                return $.extend({}, d, {
                    // Add any additional parameters you need to send to the server
                });
            },
            error: function (xhr, error, thrownError) {
                console.error("AJAX error:", xhr, error, thrownError);
                alert("Error while processing your request");
            },
        },
        lengthChange: false,
        searching: false,
        autoWidth: true,
        paging: false,
        info: false,
        order: [],
        // order: [[6, "desc"]],
        processing: true,
        serverSide: true,
        dom : 't',
        columns: [
            {
                data: "NAME",
                render: function (data, type, row) {
                    let returnVal = data;
                    if (row.hst_enforced == 1)
                        returnVal += '<span class="hstEnforced">*</span>';
                    return returnVal;
                },
                width: "50%"
            },
            {
                data: "rate",
                render: function (data, type, row) {
                    return "$" + parseFloat(data);
                },
                width: "10%"
            },
            {
                data: "quantity",
                render: function (data, type, row) {
                    return parseFloat(data);
                },
                width: "10%"
            },
            {
                data: "subTotal",
                render: function (data, type, row) {
                    return "$" + parseFloat(data);
                },
                width: "10%"
            },
            {
                data: "hst_calculated",
                render: function (data, type, row) {
                    return "$" + parseFloat(data);
                },
                width: "10%"
            },
        ],
        rowGroup: {
            dataSrc: "category_name",
            startRender: null,
            endRender: function (rows, group) {
                var quantitySum = 0;
                var SubTotalSum = 0;
                var HSTSum = 0;

                rows.every(function (rowIdx, tableLoop, rowLoop) {
                    HSTSum += parseFloat(this.data()["hst_calculated"]);
                    SubTotalSum += parseFloat(this.data()["subTotal"]);
                    quantitySum += parseFloat(this.data()["quantity"]);
                });

                return $("<tr/>")
                    .append('<td colspan="2">' + group + "</td>")
                    .append("<td>" + quantitySum + "</td>")
                    .append("<td>$" + SubTotalSum.toFixed(2) + "</td>")
                    .append("<td>$" + HSTSum.toFixed(2) + "</td>");
            },
        },
        columnDefs: [
            // { targets: [1], visible: false },
            { orderable: false, targets: '_all' }
        ],
    });
});
