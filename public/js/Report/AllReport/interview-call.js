(function ($) {
    'use strict';
  
    $(document).ready(function () {
        // Initialize Nepali Datepicker
        $('#joiningDateNepali').nepaliDatePicker({
            ndpYear: true,
            ndpMonth: true,
            ndpYearCount: 50,
            // Add any other necessary options
        });
  
        // Initialize jQuery UI Datepicker
        $("#joiningDateEnglish").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-M-yy', // Set the desired date format
            onSelect: function (dateText, inst) {
                // Format the selected date
                var selectedDate = new Date(dateText);
                var day = selectedDate.getDate();
                var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                var month = monthNames[selectedDate.getMonth()];
                var year = selectedDate.getFullYear().toString().slice(-2);
  
                var formattedDate = day + '-' + month + '-' + year;
                $(this).val(formattedDate);
            }
        });
  
        // Counter to track the number of evaluator forms
        let evaluatorCount = 0;
  
        // Add evaluator button click event
        $('#add-evaluator').on('click', function () {
            evaluatorCount++; // Increment the counter
            const evaluatorContainer = $('#evaluatorContainer');
           evaluatorContainer.empty(); // Clear previous evaluator forms
  
            // Add evaluator forms based on the current count
            for (let i = 1; i <= evaluatorCount; i++) {
                const evaluatorForm = `
                    <div class="evaluator-form" id="evaluator-${i}">
                        <h3>Evaluator ${i} <span class="close-evaluator" style="cursor: pointer;">&times;</span></h3>
                        <div class="form-group evaluator">
                            <label for="evaluator_name_${i}" class="large">Evaluator Name ${i}</label>
                            <input type="text" name="evaluator_name_${i}" placeholder="Enter Evaluator Name ${i}" class="form-control">
                        </div>
                        <div class="form-group evaluator">
                            <label for="evaluator_remarks_${i}" class="large">Evaluator Remarks ${i}</label>
                            <textarea name="evaluator_remarks_${i}" placeholder="Enter Evaluator Remarks ${i}" class="form-control"></textarea>
                        </div>
                        <div class="form-group evaluator">
                            <label for="evaluator_rating_${i}" class="large">Evaluator Rating ${i}</label>
                            <select name="evaluator_rating_${i}" class="form-control">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                `;
                evaluatorContainer.append(evaluatorForm);
            }
  
            // Show the evaluator fields row
            $('#evaluatorFields').show();
        });
  
        // Close evaluator form on clicking the close button
        $(document).on('click', '.close-evaluator', function () {
            // Remove the parent evaluator form
            $(this).closest('.evaluator-form').remove();
            evaluatorCount--; // Decrease evaluator count
        });
  
        // Initialize select2 for dropdowns
        $('select').select2();
    });
  })(window.jQuery);
  