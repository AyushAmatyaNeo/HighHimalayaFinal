(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#performanceSetupTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:PERFORMANCE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:PERFORMANCE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "PERFORMANCE_ID", title: "Performance Id", width: 50},
            {field: "CATEGORY_NAME", title: "Category Name", width: 50},
            {field: "CREDIT", title: "Credit", width: 180},
            {field: "PERFORMANCE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Performance List');

        app.searchTable('employeeSetupTable', ['PERFORMANCE_ID', 'CATEGORY_NAME','CREDIT']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'PERFORMANCE_ID': 'Performance Id',
                'CATEGORY_NAME': 'Category Name',
                'CREDIT': 'Credit'
            }, 'Performance List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
               'PERFORMANCE_ID': 'Performance Id',
                'CATEGORY_NAME': 'Category Name',
                'CREDIT': 'Credit'
            }, 'Performance List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
    });
})(window.jQuery);

