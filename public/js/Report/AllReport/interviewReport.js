(function ($) {
    'use strict';

    $(document).ready(function () {
        var $table = $('#vacancyTable');
        // var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Vacancy Status" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-address-card"></i></a>' : '';
        // var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var editInterviewAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var action = editInterviewAction;

        app.initializeKendoGrid($table, [
            {field: "ID", title: "ID"},
            {field: "FULL_NAME", title: "Full Name"},
            {field: "PERMANENT_ADDRESS", title: "Permanent Address"},
            {field: "TEMPORARY_ADDRESS", title: "Temporary Address"},
            {field: "CONTACT_NO", title: "Contact Number"},
            {field: "PREVIOUS_COMPANY", title: "Previous Compnay"},
            {field: "PREVIOUS_POSITION", title: "Previous Position"},
            {field: "PREVIOUS_SALARY", title: "Previous salary"},
            {field: "JOB_SHIFTING_REASON", title: "Job Shift Reason"},
            {field: "VEHICLE_YN", title: "Vehicle Y/N"},
            {field: "EXPECTED_SALARY", title: "Expected salary"},
            {field: "NEGOTIATION", title: "Negotation"},
            {field: "JOINING_DATE", title: "Joining Date"},
            {field: "REMARKS", title: "Remarks"},
            {field: "ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Vacancy List');

        app.searchTable('vacancyTable', ["FULL_NAME", "JOINING_DATE"]);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ID': 'ID',
                'FULL_NAME': 'Full Name',
                'PERMANENT_ADDRESS': 'Permanent Address',
                'TEMPORARY_ADDRESS': 'Temporary Address',
                'CONTACT_NUMBER': 'Contact Number',
            }, 'InterView Report List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ID': 'ID',
                'FULL_NAME': 'Full Name',
                'PERMANENT_ADDRESS': 'Permanent Address',
                'TEMPORARY_ADDRESS': 'Temporary Address',
                'CONTACT_NUMBER': 'Contact Number',
            }, 'InterView Report List');
        });

        
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
            // Handle error
        });
    });
})(window.jQuery);
