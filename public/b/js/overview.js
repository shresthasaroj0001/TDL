$(function () {
    var table = $("#mytable").DataTable();
    var url = $("#urls").val();

    // $('#exampleModal').modal('show');

    console.log("Hello");

    $("#btnsearch").click(function (e) {
        e.preventDefault();
        var period = $("#periodId").val();
        if (period == "" || period == " ") {
            period = 0;
        }

        // var category_id = $('#categoryId').val();
        // if(category_id == '' || category_id == ' '){
        //     category_id = 0;
        // }

        // var category_list_id = $('#categoryListId').val();
        // if(category_list_id == '' || category_list_id == ' '){
        //     category_list_id = 0;
        // }

        var params = { period: period };
        // var params = { 'period':period, 'category_id':category_id, 'category_list_id':category_list_id };
        var new_url = url + "?" + jQuery.param(params);

        //console.log(new_url);
        location.href = new_url;
    });

    $("#accordionExample").on("click", "#headingOne", function () {
        if ($("#collapseOne").hasClass("show")) {
            $("#collapseOne").removeClass("show");
        } else {
            $("#collapseOne").addClass("show");
        }

        //accordion-collapse
        //class="accordion-collapse collapse"
    });

    var periodId = 0;
    var RowId = 0;
    var button;
    $("#tblmonthly").on("click", ".action-update", function () {
        RowId = 0;
        var period = $("#periodId").val();
        if (period == "" || period == " ") {
            period = 0;
        }
        if (period == 0) {
            alert("Please select valid billing period");
            return;
        }
        periodId = period;

        // $("#mytbl .action-delete").click(function () {
        button = null;
        button = $(this);
        //(button.attr('rowid'));

        var result = confirm("Are You Sure You want to Update ?");
        if (result) {
            var i = url + "/last-entry";
            RowId = button.attr("rowid");
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $("#tokken").val(),
                },
                url: i,
                type: "GET",
                data: {
                    category_list_id: RowId,
                },
                beforeSend: function () {
                    $("#modal-entry").modal("show");
                    $("#UpdateModalBtn").attr("disabled", true);
                    $("#UpdateModalBtn").html("Please wait ...");

                    $("#previousPeriod").html("N/A");
                    $("#previousHstAmt").html("N/A");
                    $("#previousTotalAmt").html("N/A");

                    $("#proposedPeriod").html(
                        $("#periodId option:selected").text()
                    );
                },
                success: function (ddata) {
                    console.log(ddata);

                    // if (ddata == 0) {
                    //     alert("Internal Error");
                    //     return false;
                    // }

                    if ("isSuccess" in ddata) {
                        if (ddata.isSuccess) {
                            // console.log(ddata);
                            $("#UpdateModalBtn").attr("disabled", false);

                            $("#UpdateModalBtn").html("Submit");

                            if (ddata.data.length > 0) {
                                $("#previousPeriod").html(ddata.data[0].name);
                                $("#previousHstAmt").html(
                                    "$" + ddata.data[0].hst_amt
                                );
                                $("#previousTotalAmt").html(
                                    "$" + ddata.data[0].total_amt
                                );

                                $("#proposedHstAmt").val(ddata.data[0].hst_amt);
                                $("#proposedTotalAmt").val(
                                    ddata.data[0].total_amt
                                );
                                $("#proposedHstAmt").focus();
                            }

                            $("#proposedPeriod").html(
                                $("#periodId option:selected").text()
                            );
                        } else {
                            alert("failed");
                        }
                    }
                },
                fail: function (ddata) {
                    console.log(ddata);

                    $("#modal-entry").modal("hide");
                    alert("Error while fetching previous data");
                },
            });
        }
    });

    $("#UpdateModalBtn").on("click", function () {
        //validation
        var hst_amt = $("#proposedHstAmt").val();
        var total_amt = $("#proposedTotalAmt").val();

        if (parseFloat(hst_amt)) {
            var proposedHstAmt = parseFloat(hst_amt);
        } else {
            var proposedHstAmt = 0;
        }

        console.log(proposedHstAmt);
        if (parseFloat(total_amt)) {
            var proposedTotalAmt = parseFloat(total_amt);
        } else {
            var proposedTotalAmt = 0;
        }

        if (proposedHstAmt >= proposedTotalAmt || proposedTotalAmt == 0) {
            alert("Invalid entry in HST Amount OR Total Amount");
            return;
        }

        var i = url + "/last-entry";
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $("#tokken").val(),
            },
            url: i,
            type: "POST",
            data: {
                category_list_id: RowId,
                period: periodId,
                hst: proposedHstAmt,
                total: proposedTotalAmt,
            },
            success: function (ddata) {
                console.log(ddata);

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
            beforeSend: function () {
                $("#modal-entry").modal("show");
                $("#UpdateModalBtn").attr("disabled", true);
                $("#UpdateModalBtn").html("Please wait ...");
            },
            complete: function () {
                $("#modal-entry").modal("hide");
            },
            fail: function (ddata) {
                console.log(ddata);
                alert("Error while processing your request");
            },
        });
    });
});
