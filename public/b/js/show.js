$(function () {
    // var table = $("#mytable").DataTable();

    const editor = new DataTable.Editor({
        ajax: '../php/staff.php',
        fields: [
            {
                label: 'Name:',
                name: 'name'
            },
            {
                label: 'Description:',
                name: 'desc'
            }
        ],
        table: '#modal-column'
    });

    //https://editor.datatables.net/examples/inline-editing/fullRowCreate.html
    const columntable = new DataTable('#reportcolumntable', {
        ajax: '../php/staff.php',
        buttons: [
            {
                extend: 'createInline',
                editor,
                formOptions: {
                    submitTrigger: -2,
                    submitHtml: '<i class="fa fa-play"/>'
                }
            }
        ],
        columns: [
            { data: 'name' },
            { data: 'desc' },
            // { data: 'salary', render: DataTable.render.number(null, null, 0, '$') },
            {
                data: null,
                defaultContent: '<i class="fa fa-pencil"/>',
                className: 'row-edit dt-center',
                orderable: false
            },
            {
                data: null,
                defaultContent: '<i class="fa fa-trash"/>',
                className: 'row-remove dt-center',
                orderable: false
            }
        ],
        dom: 'Bfrtip',
        select: {
            style: 'os',
            selector: 'td:first-child'
        }
    });



    $("#modal-column").modal("show");



});