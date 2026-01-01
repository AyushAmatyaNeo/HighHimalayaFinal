(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#employeeSetupTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ID", title: "ID", width: 50},
            {field: "EMPLOYEE_CODE", title: "DNM Employee Code", width: 50},
            {field: "EMPLOYEE_ID", title: "HRIS Employee ID", width: 180},
            {field: "ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Group List');

        app.searchTable('employeeSetupTable', ['EMPLOYEE_ID', 'ID','EMPLOYEE_CODE']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'EMPLOYEE_CODE': 'Group Code',
                'EMPLOYEE_ID': 'Group Name',
                'GROUP_ID': 'Group Id'
            }, 'Group List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
               'EMPLOYEE_CODE': 'Group Code',
                'GROUP_NAME': 'Group Name',
                'GROUP_ID': 'Group Id'
            }, 'Group List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
    });
})(window.jQuery);

