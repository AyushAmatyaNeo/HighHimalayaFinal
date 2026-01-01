(function ($) {
    'use strict';

    $(document).ready(function () {
        var $table = $('#vacancyTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Interview" href="' + document.editLink + '/#:ID#" style="height:25px;" aria-label="InterView Call"> <i class="fas fa-phone" style="color: red;"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Reject" href="' + document.deleteLink + '/#:ID#" style="height:25px; aria-label="Reject"><i class="fa fa-ban"></i></a>' : '';
        
        function action(dataItem) {
            return '<a href="javascript:void(0);" onclick="openResume(\'' + dataItem.RESUME_PATH + '\')">Open PDF</a>';
        }

        window.openResume = function (resumePath) {
            // Construct the URL dynamically based on the RESUME_PATH
            var pdfUrl = 'http://hr.neosoftware.com.np:8561/vacancy/' + resumePath;

            // Open the PDF in a new window
            window.open(pdfUrl, '_blank');
        };

        var actionTemplate = editAction + deleteAction;

        app.initializeKendoGrid($table, [
            {field: "ID", title: "ID"},
            {field: "FULL_NAME", title: "Full Name"},
            {field: "POSITION", title: "Position"},
            {field: "EMAIL", title: "Email"},
            {field: "CONTACT_NUMBER", title: "Contact Number"},
            {field: "APPLICANT_QUESTION", title: "Applicant Question"},
            {field: "FEFRENCE_FROM", title: "Refrence From"},
            { field: "RESUME_PATH", title: "Resume", template: '<a href="javascript:void(0);" onclick="openResume(\'#=RESUME_PATH#\')" class="k-button k-primary" style="font-size: 14px;">View Resume</a>' },

            {field: "ID", title: "Action", width: 120, template: actionTemplate}
        ], null, null, null, 'Vacancy List');

        app.searchTable('vacancyTable', ["POSITION", "EMAIL"]);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ID': 'ID',
                'FULL_NAME': 'Full Name',
                'POSITION': 'Position',
                'EMAIL': 'Email',
                'CONTACT_NUMBER': 'Contact Number',
            }, 'Vacancy List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ID': 'ID',
                'FULL_NAME': 'Full Name',
                'POSITION': 'Position',
                'EMAIL': 'Email',
                'CONTACT_NUMBER': 'Contact Number',
            }, 'Vacancy List');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
            // Handle error
        });
    });
})(window.jQuery);
