(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#leaveTable');

        app.initializeKendoGrid($table, [
            { field: "LEAVE_ENAME", title: "Leave Name" },
            { field: "PREVIOUS_YEAR_BAL_HR", title: "Previous Balance" },
            { field: "TOTAL_HOURS", title: "Total Hours" },
            { field: "LEAVE_TAKEN", title: "Leave Taken" },
            { field: "LEAVE_APP_PENDING", title: "Leave Approve Pending" },
            { field: "AVAILABLE_BALANCE", title: "Available Hours" }
        ], null, null, null, 'Leave Balance List');


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });


        app.searchTable('leaveTable', ['LEAVE_ENAME']);

        var exportMap = {
            'LEAVE_ENAME': 'Leave',
            'PREVIOUS_YEAR_BAL_HR': 'Previous Balance',
            'TOTAL_HOURS': 'Total Hours',
            'LEAVE_TAKEN': 'Leave Taken',
            'LEAVE_APP_PENDING': 'Leave Approve Pending',
            'AVAILABLE_BALANCE': 'Available Hours'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Leave Balance List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Leave Balance List');
        });


        console.log(document.currentMonth);

        app.populateSelect($('#leaveMonth'), document.monthList, 'LEAVE_YEAR_MONTH_NO', 'MONTH_EDESC', null, null, document.currentMonth)
        var $month = $('#leaveMonth');
        var $monthlyLeaveTable = $('#monthlyLeaveTable');



        app.initializeKendoGrid($monthlyLeaveTable, [
            { field: "LEAVE_ENAME", title: "Leave Name" },
            { field: "PREVIOUS_YEAR_BAL", title: "Previous Balance" },
            { field: "TOTAL_DAYS", title: "Total Days" },
            { field: "LEAVE_TAKEN", title: "Leave taken" },
            { field: "BALANCE", title: "Available Days" }
        ], null, null, null, 'Leave Balance List');


        var populateMonthlyLeave = function () {
            var value = $month.val();
            if (value == null) {
                return;
            }
            app.serverRequest("", { fiscalYearMonthNo: value }).then(function (response) {
                app.renderKendoGrid($monthlyLeaveTable, response.data);
            }, function (error) {

            });
        };

        populateMonthlyLeave();


        $month.on('change', function () {
            populateMonthlyLeave();
        });



    });
})(window.jQuery, window.app);
