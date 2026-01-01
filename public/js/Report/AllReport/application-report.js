(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#applicationReportTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
       // var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction ;
        app.initializeKendoGrid($table, [
            {field: "ID", title: "ID", width: 100, locked:true},
    {title: "Application Information", locked:true, columns: [
        {field: "FULL_NAME", title: "Name",width:100 ,locked:true},
        {field: "POSITION", title: "Applied For",width:100 ,locked:true}
    ]},
    {field: "EVALUATOR_NAME_1", title: "Evaluator Name 1", width: 150},
    {field: "EVALUATOR_RATING_1", title: "Evaluator Rating 1", width: 150},
    {field: "EVALUATOR_NAME_2", title: "Evaluator Name 2", width: 150},
    {field: "EVALUATOR_RATING_2", title: "Evaluator Rating 2", width: 150},
    {field: "EVALUATOR_NAME_3", title: "Evaluator Name 3", width: 150},
    {field: "EVALUATOR_RATING_3", title: "Evaluator Rating 3", width: 150},
    {field: "AVERAGE", title: "Average Rating", width: 150},
    {field: "VACANCY_STATUS", title: "Status", width: 170},
    {field: "ID", title: "Action", width: 130, template: action}
        ], null, null, null, 'Vacancy List');

        app.searchTable('applicationReportTable', ["POSITION", "EMAIL"]);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                 'ID': 'ID',
                 'FULL_NAME': 'Full Name',
                 'POSITION' : 'Position',
                 'VACANCY_STATUS' : 'Status'

                // 'BRANCH_NAME': 'Branch Name',
            }, 'Vacancy List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ID': 'ID',
                 'FULL_NAME': 'Full Name',
                 'POSITION' : 'Position',
                 'VACANCY_STATUS' : 'Status'
            }, 'Vacancy List');
        });

        $('.searchBtn').on('click',function(){
            var buttonValue = $(this).data('value');
            app.pullDataById(document.pullApplicationReport,{
                'status': buttonValue
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);