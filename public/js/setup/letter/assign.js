(function ($, app) {
    "use strict";
    $(document).ready(function () {
        let $employeeId = $("#employeeId");
        let $letterId = $("#letterId");
        let assignUrl = document.assignLetter;
        var $table = $('#viewEmployeeList');

        $("select").select2();

        app.populateSelect($employeeId, document.employeeLists, "EMPLOYEE_ID", "FULL_NAME", "Select One");

        $("#submit").on('click', function () {
            let isValid = true;
            let employeeIds = $employeeId.val();
            let letterId = $letterId.val();
            let selectedOptions = $("input[name='options[]']:checked").map(function () {
                return $(this).val();
            }).get();

            if (!employeeIds || employeeIds.length === 0) {
                $employeeId.siblings('.errorMsg').show();
                isValid = false;
            } else {
                $employeeId.siblings('.errorMsg').hide();
            }

            if (!letterId) {
                $letterId.siblings('.errorMsg').show();
                isValid = false;
            } else {
                $letterId.siblings('.errorMsg').hide();
            }

            if (isValid) {
                let formData = {
                    employeeIds: employeeIds,
                    letterId: letterId,
                    childLetterIds: selectedOptions

                };

                $.ajax({
                    url: assignUrl,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            app.showMessage('Assignment successful', 'success');

                            $employeeId.val(null).trigger('change');
                            $letterId.val(null).trigger('change');
                        } else {
                            app.showMessage(response.error, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        app.showMessage('An error occurred: ' + error, 'error');
                    }
                });
            }
        });

        $letterId.change(function (e) {
            app.serverRequest(document.getChilds, {
                letterId: $(this).val()
            }).then(function (response) {
                if (!response.success) {
                    app.showMessage(response.error, 'error');
                    return;
                }

                $("#checkbox-container").empty();
                if (response.data.length > 0) {
                    $("#childLabel").show();

                    response.data.forEach(function (item) {
                        var checkboxHtml = `
                            <label>
                                <input type="checkbox" name="options[]" value="${item.LETTER_SETUP_ID}"> ${item.LETTER_TITLE}
                            </label>
                        `;
                        $("#checkbox-container").append(checkboxHtml);
                    });
                } else {
                    $("#childLabel").hide();
                }

            }, function (error) {
                app.showMessage(error, 'error');
            });
        });
        app.initializeKendoGrid($table, [
            { field: "FULL_NAME", title: "FULL NAME", width: 70 },
            { field: "LETTER_TITLE", title: "Letter Name", width: 70 },
            { field: "BRANCH_NAME", title: "BRANCH", width: 70 },
            { field: "COMPANY_NAME", title: "COMPANY", width: 70 },
            { field: "DEPARTMENT_NAME", title: "DEPARTMENT", width: 70 },
            { field: "DESIGNATION_TITLE", title: "DESIGNATION", width: 70 },
            {
                title: "Action",
                width: 70,
                template: `
                    <a href="\${document.viewDocumentUrl}/#: LETTER_SETUP_ID #/#: EMPLOYEE_ID #"
                    class="btn btn-primary btn-xs">
                        <i class="fa fa-eye"></i>
                    </a>
                `
            }
        ]
        );

        $("#view").on('click', function () {
            let employeeIds = $employeeId.val();
            let letterId = $letterId.val();

            let isValid = true;

            if (!employeeIds || employeeIds.length === 0) {
                $employeeId.siblings('.errorMsg').show();
                isValid = false;
            } else {
                $employeeId.siblings('.errorMsg').hide();
            }

            if (!letterId) {
                $letterId.siblings('.errorMsg').show();
                isValid = false;
            } else {
                $letterId.siblings('.errorMsg').hide();
            }

            if (isValid) {
                app.serverRequest(document.viewEmpList, {
                    empIds: employeeIds,
                    letterId: letterId
                }).then(function (success) {
                    app.renderKendoGrid($table, success.data);
                }, function (failure) { });
            }
        });

        app.searchTable('viewEmployeeList', ['FULL_NAME', 'BRANCH_NAME', 'COMPANY_NAME', 'DEPARTMENT_NAME']);

    });
})(window.jQuery, window.app);