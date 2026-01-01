(function ($, app) {
//    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('deductionDt', 'nepaliStartDate1');

        var $employee = $('#employeeId');
        var $leave = $('#leaveId');
        var $halfDay = $("#halfDay");
        var $availableHours = $('#availableHours');
        var $noOfHours = $('#noOfHours');
        var $request = $("#request");
        var $errorMsg = $("#errorMsg");
        var $deductionDt = $('#deductionDt');
        var leaveName = "";
        var leaveList = [];
        var availableHours = null;
        app.floatingProfile.registerListener(function (data) {
        });
        app.floatingProfile.setDataFromRemote($employee.val());

        var $form = $('#leaveDeduction');
        var checkForErrors = function () {
            var noOfHours = parseFloat($noOfHours.val());
            if ((availableHours != "" && noOfHours != "") && noOfHours > availableHours) {
                $("#errorMsg").html("* Applied days can't be more than available days");
                $("#request").attr("disabled", "disabled");
            } else if ((availableHours != "" && noOfHours != "") && (noOfHours <= availableHours)) {
                $("#errorMsg").html("");
                $("#request").removeAttr("disabled");
            }
        }

        app.setLoadingOnSubmit("leaveDeduction", function ($form) {
            if ($form.prop('valid') === 'true') {
                return true;
            } else {
                app.showMessage($form.prop('error-message'), 'error');
                return false;
            }
        });

        var leaveChange = function (obj) {
            var $this = $(obj);
            if ($this.val() === null || $this.val() === '' || $this.val() === '-1') {
                return;
            }
            checkForErrors();
            App.blockUI({target: "#hris-page-content", message: "Calculating Leave Days"});
            var deductionDtValue = $deductionDt.val();
            app.pullDataById(document.wsPullLeaveDetail, {
                'leaveId': $this.val(),
                'employeeId': $employee.val(),
                'startDate': (deductionDtValue == '') ? null : deductionDtValue
            }).then(function (success) {
                App.unblockUI("#hris-page-content");
                var leaveDetail = success.data;
                substituteDetails = success.subtituteDetails;
                availableHours = (typeof leaveDetail.BALANCE_HR == 'undefined') ? 0 : parseFloat(leaveDetail.BALANCE_HR);
                $availableHours.val(availableHours);
                var noOfHours = parseFloat($noOfHours.val());
                if ((availableHours != "" && noOfHours != "") && noOfHours > availableHours) {
                    $("#errorMsg").html("* Applied days can't be more than available days");
                    $("#request").attr("disabled", "disabled");
                } else if ((availableHours != "" && noOfHours != "") && (noOfHours <= availableHours)) {
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                }

                leaveName = leaveDetail.LEAVE_ENAME;
            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        };

        $leave.on('change', function () {
            leaveChange(this);
        });

        var employeeChange = function (obj) {
            var $this = $(obj);
            app.floatingProfile.setDataFromRemote($this.val());
            App.blockUI({target: "#hris-page-content", message: "Fetching Employee Leaves"});
            app.pullDataById(document.wsPullLeaveDetailWidEmployeeId, {
                'employeeId': $this.val()
            }).then(function (success) {
                App.unblockUI("#hris-page-content");
                leaveList = success.data;
                app.populateSelect($leave, leaveList, 'id', 'name', 'Select a Leave', null, null, false);

            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        };

        $employee.on('change', function () {
            employeeChange(this);
        });

        $noOfHours.on('change', function() {
            var noOfHours = parseFloat($noOfHours.val());
            if ((availableHours != "" && noOfHours != "") && noOfHours > availableHours) {
                $("#errorMsg").html("* Applied days can't be more than available days");
                $("#request").attr("disabled", "disabled");
            } else if ((availableHours != "" && noOfHours != "") && (noOfHours <= availableHours)) {
                $("#errorMsg").html("");
                $("#request").removeAttr("disabled");
            }
        })

    });

})(window.jQuery, window.app);


