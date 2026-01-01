(function($) {
    'use strict';
    $(document).ready(function() {
        $('select').select2();

        function updateStyles() {
            var topValue = $('#top').val() ? $('#top').val() + 'mm' : '';
            var bottomValue = $('#bottom').val() ? $('#bottom').val() + 'mm' : '';
            var leftValue = $('#left').val() ? $('#left').val() + 'mm' : '';
            var rightValue = $('#right').val() ? $('#right').val() + 'mm' : '';
            var $letterContents = $('.letter-content');

            if ($('#CustomStyle').is(':checked')) {
                $letterContents.css({
                    'top': topValue,
                    'bottom': bottomValue,
                    'left': leftValue,
                    'right': rightValue
                });
            } else {
                $letterContents.css({
                    'top': '',
                    'bottom': '',
                    'left': '',
                    'right': ''
                });
            }
        }
        updateStyles();
        $('#top, #bottom, #left, #right').on('input', updateStyles);
        $('#CustomStyle').on('change', updateStyles);

        $('#downloadPdf').on('click', function(e) {
            e.preventDefault();
            var letterTitle = $('#docName').val();
            var elements = document.querySelectorAll('.letter-head');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 210;
            const pageHeight = 297;

            const elementsArray = Array.from(elements);

            function elementToCanvas(element) {
                return html2canvas(element, { scale: 2 });
            }

            function addImageToPdf(canvas, pdf, index) {
                const imgData = canvas.toDataURL('image/jpeg', 2.0);
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let positionY = 0;
                let remainingHeight = imgHeight;

                if (index > 0) {
                    pdf.addPage();
                }

                while (remainingHeight > 0) {
                    const scaledImgHeight = Math.min(pageHeight, remainingHeight);
                    pdf.addImage(imgData, 'JPEG', 0, positionY, imgWidth, scaledImgHeight);
                    remainingHeight -= scaledImgHeight;
                    positionY -= scaledImgHeight;
                }
            }

            elementsArray.reduce((promise, element, index) => {
                    return promise
                        .then(() => elementToCanvas(element))
                        .then(canvas => addImageToPdf(canvas, pdf, index));
                }, Promise.resolve())
                .then(() => {
                    pdf.save(letterTitle + '.pdf');
                })
                .catch(error => {
                    console.error("Error generating PDF:", error);
                });
        });

    });
})(window.jQuery);