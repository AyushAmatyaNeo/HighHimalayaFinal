(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#ratingTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "NAME", title: "Name", width: 100},
            {field: "TYPE", title: "Type", width: 100},
            {field: "VALUE", title: "Value", width: 100},
            {field: "ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Payroll CTC Setup');

        app.searchTable('ratingTable', ['NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'NAME': 'Name',
                'TYPE': 'Type',
                'VALUE': 'Value'
            }, 'Payroll CTC Setup');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'NAME': 'Name',
                'TYPE': 'Type',
                'VALUE': 'Value'
            }, 'Payroll CTC Setup');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);