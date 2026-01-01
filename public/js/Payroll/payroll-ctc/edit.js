(function ($) {
    'use strict';
    $(document).ready(function () {
        // Cache the DOM elements
        var flatIdSelect = document.getElementById("flatId");
        var valueFieldWrapper = document.getElementById("value-field-wrapper");
        var valueField = document.getElementById("value");

        // Function to check the value of flatId and toggle visibility and required attribute of value input
        function toggleValueField() {
            if (flatIdSelect.value === "") {
                // If flatId is not selected (empty), show value field and make it required
                valueFieldWrapper.style.display = "block";
                valueField.setAttribute("required", "true");
            } else {
                // If flatId is selected, hide value field and remove required attribute
                valueFieldWrapper.style.display = "none";
                valueField.removeAttribute("required");
            }
        }

        // Trigger the toggle on page load to check the initial state
        toggleValueField();

        // Add event listener to the flatId select element to trigger toggle on change
        flatIdSelect.addEventListener("change", toggleValueField);
    });
})(window.jQuery);
