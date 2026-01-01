(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#vacancyTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:VACANCY_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:VACANCY_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "VACANCY_ID", title: "Vacancy Id", width: 70},
            {field: "POSITION", title: "Position", width: 150},
            {field: "AVAILABILITY", title: "Avaliability", width: 70},
            {field: "DESCRIPTION", title: "Description", width: 280},
            {field: "VACANCY_ID", title: "Action", width: 70, template: action}
        ], null, null, null, 'Vacancy List');

        app.searchTable('vacancyTable', ['POSITION', 'VACANCY_ID']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'VACANCY_ID': 'Vacancy Id',
                'POSITION': 'Position',
            }, 'Vacancy List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'VACANCY_ID': 'Vacancy Id',
                'POSITION': 'Position',
            }, 'Vacancy List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
    });
})(window.jQuery);

