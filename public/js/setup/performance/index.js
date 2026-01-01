(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#performance');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:INDEX_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:INDEX_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "INDEX_ID", title: "Index Id", width: 50},
            {field: "PERCENT_RANGE", title: "Percent Range", width: 60},
            {field: "PERCENT_DESC", title: "Percent Description", width: 60},
            {field: "INDEX_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Performance List');

        app.searchTable('employeeSetupTable', ['INDEX_ID', 'PERCENT_RANGE','PERCENT_DESC']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'INDEX_ID': 'Index Id',
                'PERCENT_RANGE': 'Percent Range',
                'PERCENT_DESC': 'Percent Description'
            }, 'Performance List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'INDEX_ID': 'Index Id',
                'PERCENT_RANGE': 'Percent Range',
                'PERCENT_DESC': 'Percent Description'
            }, 'Performance List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
    });
})(window.jQuery);

