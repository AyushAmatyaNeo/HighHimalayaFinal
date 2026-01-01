(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        app.datePickerWithNepali('eventDate', 'eventDateNepali');
        app.datePickerWithNepali('toContractExpiryDate', 'contractExpiryDtNepali');

        var $employeeId = $("#employeeId");
        var $serviceEventTypeId = $("#serviceEventTypeId");

        var $toServiceTypeId = $('#toServiceTypeId');
        var $toCompanyId = $('#toCompanyId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');

        $employeeId.parent().css('pointer-events', 'none');
        
        var showHistory = function (employeeId) {
            app.pullDataById(document.wsGetHistoryList, { employeeId }).then(function (response) {
                console.log(response);
                if (response.success) {
                    var data = [];
                    var services = response.data;
        
                    $.each(services, function (key, item) {
                        var serviceEventContent = '<table class="table">';
        
                        // Conditional fields based on serviceEvent
                        if (item['SERVICE_EVENT_TYPE_NAME'] === 'Appointment' || item['SERVICE_EVENT_TYPE_NAME'] === 'Promotion' || item['SERVICE_EVENT_TYPE_NAME'] === 'Company Transfer') { console.log("hello");
                            serviceEventContent += `
                            <tr><td><strong>Company</strong></td><td>${item['COMPANY_NAME']}</td></tr>
                            <tr><td><strong>Branch</strong></td><td>${item['BRANCH_NAME']}</td></tr>
                            <tr><td><strong>Department</strong></td><td>${item['DEPARTMENT_NAME']}</td></tr>
                            <tr><td><strong>Designation</strong></td><td>${item['DESIGNATION_TITLE']}</td></tr>
                            <tr><td><strong>Position</strong></td><td>${item['POSITION_NAME']}</td></tr>
                            <tr><td><strong>Service Type</strong></td><td>${item['SERVICE_TYPE_NAME']}</td></tr>
                            <tr><td><strong>Salary</strong></td><td>${item['TO_SALARY']}</td></tr>
                        `;
                        }
        
                        // Conditional fields based on serviceEvent
                        if (item['SERVICE_EVENT_TYPE_NAME'] === 'Resignation') {
                            serviceEventContent += `
                                <tr><td><strong>Service Type</strong></td><td>${item['SERVICE_TYPE_NAME']}</td></tr>
                                <tr><td><strong>Resignation Date</strong></td><td>${item['EVENT_DATE'] || 'N/A'}</td></tr>
                            `;
                        }

                        if (item['SERVICE_EVENT_TYPE_NAME'] === 'Retirement') {
                            serviceEventContent += `
                                <tr><td><strong>Service Type</strong></td><td>${item['SERVICE_TYPE_NAME']}</td></tr>
                                <tr><td><strong>Retirement Date</strong></td><td>${item['EVENT_DATE'] || 'N/A'}</td></tr>
                            `;
                        }

                        if (item['SERVICE_EVENT_TYPE_NAME'] === 'Suspend') {
                            serviceEventContent += `
                                <tr><td><strong>Service Type</strong></td><td>${item['SERVICE_TYPE_NAME']}</td></tr>
                                <tr><td><strong>Suspend Date</strong></td><td>${item['EVENT_DATE'] || 'N/A'}</td></tr>
                            `;
                        }
        
                        if (item['SERVICE_EVENT_TYPE_NAME'] === 'Contract Extension') {
                            serviceEventContent += `
                                <tr><td><strong>Service Type</strong></td><td>${item['SERVICE_TYPE_NAME']}</td></tr>
                                <tr><td><strong>Contract Expiry Date</strong></td><td>${item['TO_CONTRACT_EXPIRY_DATE'] || 'N/A'}</td></tr>
                            `;
                        }
        
                        // Close the table tag
                        serviceEventContent += '</table>';
        
                        // Wrap all content into a card or div for the timeline
                        data.push({
                            time: item['EVENT_DATE'],
                            header: item['SERVICE_EVENT_TYPE_NAME'],
                            body: [{
                                tag: 'div',
                                content: serviceEventContent
                            }],
                        });
                    });
        
                    // Use Albe Timeline or similar timeline plugin
                    $('#myTimeline').albeTimeline(data);
                }
            }, function () {
                // Handle error if any
            });
        };

        var $serviceEvent = 'Appointment'; // Default service event

        // Mapping of value IDs to service event types
        var serviceEventMap = {
            2: 'Appointment',
            3: 'Promotion',
            5: 'Resignation',
            8: 'Retirement',
            14: 'Suspend',
            17: 'Company Transfer',
            18: 'Contract Extension'
        };

        // Event listener for the service event type dropdown change
        $('#serviceEventTypeId').on('change', function () {
            var $this = $(this);
            var value = $this.val(); // Get selected value from dropdown

            // Update the serviceEvent based on selected value
            $serviceEvent = serviceEventMap[value] || 'Appointment'; // Default to 'Appointment' if no match

            // Hide all divs first
            $('.event-div').hide();

            // Disable all form fields in the hidden divs and remove the `required` attribute
            $('.event-div input, .event-div select').prop('disabled', true).removeAttr('required');

            // Show the corresponding div based on the selected serviceEvent and enable the relevant form fields
            if ($serviceEvent == 'Appointment' || $serviceEvent == 'Promotion' || $serviceEvent == 'Company Transfer') {
                $('#appointmentDiv').show();
                $('#appointmentDiv input, #appointmentDiv select').prop('disabled', false).attr('required', 'required');
            } else if ($serviceEvent == 'Resignation' || $serviceEvent == 'Retirement' || $serviceEvent == 'Suspend') {
                $('#resignationDiv').show();
                $('#resignationDiv input, #resignationDiv select').prop('disabled', false).attr('required', 'required');
            } else if ($serviceEvent == 'Contract Extension') {
                $('#contractExtensionDiv').show();
                $('#contractExtensionDiv input, #contractExtensionDiv select').prop('disabled', false).attr('required', 'required');
            }
        });

        // Trigger change event to initialize the form based on the initial value
        $('#serviceEventTypeId').trigger('change');

        showHistory($employeeId.val());
        app.setDropZone($('#fileId'), $('#dropZone'), document.uploadFileLink);
    });
})(window.jQuery, window.app);


