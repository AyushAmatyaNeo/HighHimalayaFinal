(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $operation = $('#operation');
        var $module = $('#module');
        app.searchTable('logReport', ['ID','MODULE','IP_ADDRESS','HOST_NAME','CREATED_BY','CREATED_DT','MODIFIED_BY','MODIFIED_DT','DELETED_BY','DELETED_DT','CREATED_DESC','MODIFIED_DESC','DELETED_DESC','TABLE_DESC'], false);
        var $search = $('#search');
        var $logReport = $('#logReport');

        var map = {
            'MODULE': 'Module'
        };

        app.initializeKendoGrid($logReport, [
            { field: "ID", title: "S.N", width: 100, locked: true},
            { field: "OPERATION", title: "Operation", width: 100,locked: true},
            { field: "MODULE", title: "Module", width: 100,locked: true},
            { field: "CREATED_DESC", title: "Created Description", width: 250},
            { field: "MODIFIED_DESC", title: "Modified Description", width: 250},
            { field: "DELETED_DESC", title: "Deleted Description", width: 250},
            { field: "IP_ADDRESS", title: "Ip Address", width: 100},
            { field: "HOST_NAME", title: "Host Name", width: 100},
            { field: "CREATED_BY", title: "Created By", width: 100},
            { field: "CREATED_DT", title: "Created Date", width: 100},
            { field: "MODIFIED_BY", title: "Modified By", width: 100},
            { field: "MODIFIED_DT", title: "Modified Date", width: 100},
            { field: "DELETED_BY", title: "Deleted By", width: 100},
            { field: "DELETED_DT", title: "Deleted Date", width: 100},           
            { field: "TABLE_DESC", title: "Table Desc", width: 100}

        ], null, null, null, 'Log Report.xlsx');

        $search.on('click', function () {
            var operation = $operation.val();
            var module = $module.val();
            var data = document.searchManager.getSearchValues();
            data['operation'] = operation;
            data['module'] = module;

            
            
            // console.log($data);
            app.serverRequest(document.logWs, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($logReport, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        $('#export').on('click', function () {
            app.excelExport($logReport, map, 'Log Report.xlsx');
        });

    });
})(window.jQuery, window.app);