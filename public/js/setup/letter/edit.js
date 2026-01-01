(function($) {
    'use strict';
    $(document).ready(function() {
        $('select').select2();

        initializeExistingEditors(editorCount);

        $('#addPage').click(function(e) {
            e.preventDefault();
            initializeEditor(editorCount);
            editorCount++;
        });

        function initializeExistingEditors(count) {
            for (let i = 0; i < count; i++) {
                initializeEditor(i, true);
            }
        }

        // var selectedValues = [];
        // $('input[name="checkboxName[]"]:checked').each(function() {
        //     selectedValues.push($(this).val());
        // });

        function initializeEditor(index, isExisting = false) {
            var imageUrl = $('#ImageUrl').val();
            if (!isExisting) {
                var newEditorHtml = `
                <div class="form-group">
                    <label class="control-label">Page ${index + 1}</label>
                    <div class="summernote" id="summernote-${index}"></div>
                    <input name="description_${index}" type="hidden" id="description-${index}" value="">
                </div>`;
                var newLetterHeadHtml = `
                <div class="letter-head" id="letter-head-${index}" style="margin-top: 20px;">
                    <img src="${imageUrl}" alt="Letterhead" class="letter-image" id="image-${index}">
                    <div id="rendered-content-${index}" class="letter-content"></div>
                    <label for="" class="page-number">Page ${index + 1}</label>    
                </div>`;
                $('.tab-content').append(newEditorHtml);
                $('#letter-head-container').append(newLetterHeadHtml);
            }

            var searchUrl = document.searchurl;

            $(`#summernote-${index}`).summernote({
                height: 800,
                minHeight: null,
                maxHeight: null,
                focus: true,
                hint: {
                    words: [],
                    match: /\b(\w{1,})$/,
                    search: function(keyword, callback) {
                        let cachedData = getCachedData(keyword);
                        if (cachedData) {
                            callback(cachedData);
                        } else {
                            $.ajax({
                                url: searchUrl,
                                data: { query: keyword },
                                success: function(data) {
                                    storeCachedData(keyword, data.words);
                                    callback(data.words);
                                }
                            });
                        }
                    }
                }
            }).on('summernote.change', function() {
                updateRenderedContent(index);
            });

            $('.variables').each(function() {
                var $this = $(this);
                $this.off('click').on('click', function() {
                    var variable = $this.data('variable').trim();
                    $(`#summernote-${index}`).summernote('insertText', '[' + variable + ']');
                });
            });

            function getCachedData(query) {
                let cache = JSON.parse(localStorage.getItem('autocompleteCache')) || {};
                return cache[query] || null;
            }

            function storeCachedData(query, data) {
                let cache = JSON.parse(localStorage.getItem('autocompleteCache')) || {};
                cache[query] = data;
                localStorage.setItem('autocompleteCache', JSON.stringify(cache));
            }

            if (isExisting) {
                let encodedContent = $(`#description-${index}`).val();
                let decodedContent = decodeURIComponent(encodedContent);
                $(`#summernote-${index}`).summernote('code', decodedContent);
                updateRenderedContent(index);
            }
        }

        function updateRenderedContent(index) {
            var content = $(`#summernote-${index}`).summernote('code');
            $(`#rendered-content-${index}`).html(content);
        }
        $('#nepaliType').change(function() {
            if ($(this).is(':checked')) {
                $('#language-textarea').show();
            } else {
                $('#language-textarea').hide();
            }
        });


        var switchInput = document.getElementById('nepaliType');
        var abroadAddress = document.getElementById('language-textarea');

        switchInput.addEventListener('change', function() {
            if (this.checked) {
                if (typeof nepalify !== 'undefined' && typeof nepalify.interceptElementById === 'function') {
                    nepalify.interceptElementById("language-textarea");
                }
            } else {
                var newElement = abroadAddress.cloneNode(true);
                abroadAddress.parentNode.replaceChild(newElement, abroadAddress);
                abroadAddress = newElement;
            }
        });

        $('#LetterSetupForm').submit(function(e) {
            var form = $(this);
            var hasError = false;

            form.find('.summernote').each(function(index) {
                var $this = $(this);
                var hiddenInput = form.find(`input[name='description_${index}']`);
                hiddenInput.val($this.summernote('code'));

                if (hiddenInput.val().trim() === "") {
                    var parentId = hiddenInput.closest(".form-group");
                    var errorMsgSpan = parentId.find('span.errorMsg');

                    if (errorMsgSpan.length === 0) {
                        errorMsgSpan = $('<span />', {
                            "class": 'errorMsg',
                            text: 'Message body cannot be empty'
                        });
                        parentId.append(errorMsgSpan);
                    }

                    hiddenInput.focus();
                    hasError = true;
                } else {
                    hiddenInput.closest(".form-group").find('span.errorMsg').remove();
                }
            });

            if (hasError) {
                e.preventDefault();
            }
        });


        // $(`#EmailMessage`).summernote({
        //     height: 300,
        //     minHeight: null,
        //     maxHeight: null,
        //     focus: true,
        // });

        // $('#sendEmail').on('click', function(e) {
        //     e.preventDefault();
        //     var letterTitle = 'test';
        //     var elements = document.querySelectorAll('.letter-head');
        //     const { jsPDF } = window.jspdf;
        //     const pdf = new jsPDF('p', 'mm', 'a4');
        //     const imgWidth = 210; // A4 width in mm
        //     const pageHeight = 297; // A4 height in mm

        //     const promises = [];
        //     var emailSend = document.sendMail;


        //     elements.forEach((element, index) => {
        //         promises.push(
        //             html2canvas(element, { scale: 0.8 }).then(canvas => {
        //                 const imgData = canvas.toDataURL('image/jpeg', 0.8);
        //                 const imgHeight = canvas.height * imgWidth / canvas.width;
        //                 let positionY = 0;
        //                 let remainingHeight = imgHeight;

        //                 if (index > 0) {
        //                     pdf.addPage();
        //                 }

        //                 while (remainingHeight > 0) {
        //                     const scaledImgHeight = Math.min(pageHeight, remainingHeight);
        //                     pdf.addImage(imgData, 'PNG', 0, positionY, imgWidth, scaledImgHeight);
        //                     remainingHeight -= scaledImgHeight;
        //                     positionY -= scaledImgHeight;
        //                 }
        //             })
        //         );
        //     });

        //     Promise.all(promises).then(() => {
        //         const pdfData = pdf.output('blob');

        //         var formData = new FormData();
        //         formData.append('pdf', pdfData, letterTitle + '.pdf');

        //         $.ajax({
        //             url: emailSend,
        //             type: 'POST',
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             success: function(response) {
        //                 console.log('PDF sent to server for email:', response);
        //                 alertify.success(response.message);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error('Error sending PDF:', error);
        //             }
        //         });
        //     }).catch(error => {
        //         console.error('Error generating PDF:', error);
        //     });
        // });

        // $('#uploadPdf').on('click', function(e) {
        //     e.preventDefault();
        //     var letterTitle = 'test';
        //     var elements = document.querySelectorAll('.letter-head');
        //     const { jsPDF } = window.jspdf;
        //     const pdf = new jsPDF('p', 'mm', 'a4');
        //     const imgWidth = 210; // A4 width in mm
        //     const pageHeight = 297; // A4 height in mm

        //     const promises = [];
        //     var pushDoc = document.pushDoc;


        //     elements.forEach((element, index) => {
        //         promises.push(
        //             html2canvas(element, { scale: 0.8 }).then(canvas => {
        //                 const imgData = canvas.toDataURL('image/jpeg', 0.8);
        //                 const imgHeight = canvas.height * imgWidth / canvas.width;
        //                 let positionY = 0;
        //                 let remainingHeight = imgHeight;

        //                 if (index > 0) {
        //                     pdf.addPage();
        //                 }

        //                 while (remainingHeight > 0) {
        //                     const scaledImgHeight = Math.min(pageHeight, remainingHeight);
        //                     pdf.addImage(imgData, 'PNG', 0, positionY, imgWidth, scaledImgHeight);
        //                     remainingHeight -= scaledImgHeight;
        //                     positionY -= scaledImgHeight;
        //                 }
        //             })
        //         );
        //     });

        //     Promise.all(promises).then(() => {
        //         const pdfData = pdf.output('blob');

        //         var formData = new FormData();
        //         formData.append('pdf', pdfData, letterTitle + '.pdf');

        //         $.ajax({
        //             url: pushDoc, // Update with your server endpoint
        //             type: 'POST',
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             success: function(response) {
        //                 console.log('PDF sent to server for email:', response);
        //                 alertify.success(response.message);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error('Error sending PDF:', error);
        //             }
        //         });
        //     }).catch(error => {
        //         console.error('Error generating PDF:', error);
        //     });
        // });

        $('#downloadPdf').on('click', function(e) {
            e.preventDefault();
            var letterTitle = 'test';
            var elements = document.querySelectorAll('.letter-head');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 210; // A4 width in mm
            const pageHeight = 297; // A4 height in mm

            elements.forEach((element, index) => {
                html2canvas(element, { scale: 0.5 }).then(canvas => {
                    const imgData = canvas.toDataURL('image/jpeg', 0.5);
                    const imgHeight = canvas.height * imgWidth / canvas.width;
                    let positionY = 0;
                    let remainingHeight = imgHeight;

                    if (index > 0) {
                        pdf.addPage();
                    }

                    while (remainingHeight > 0) {
                        const scaledImgHeight = Math.min(pageHeight, remainingHeight);
                        pdf.addImage(imgData, 'PNG', 0, positionY, imgWidth, scaledImgHeight);
                        remainingHeight -= scaledImgHeight;
                        positionY -= scaledImgHeight;
                    }
                });
            });

            setTimeout(() => {
                pdf.save(letterTitle + '.pdf');
            }, 1000);
        });

        $(document).ready(function() {
            var $topInput = $('#top');
            var $bottomInput = $('#bottom');
            var $leftInput = $('#left');
            var $rightInput = $('#right');
            var $letterContent = $('.letter-content');
            var $customStyle = $('#CustomStyle');

            var initialStyles = {
                top: $letterContent.css('top'),
                bottom: $letterContent.css('bottom'),
                left: $letterContent.css('left'),
                right: $letterContent.css('right')
            };

            function updateStyles() {
                if ($customStyle.is(':checked')) {
                    var topValue = $topInput.val() ? $topInput.val() + 'mm' : '';
                    var bottomValue = $bottomInput.val() ? $bottomInput.val() + 'mm' : '';
                    var leftValue = $leftInput.val() ? $leftInput.val() + 'mm' : '';
                    var rightValue = $rightInput.val() ? $rightInput.val() + 'mm' : '';
                    customStyleInputs.style.display = 'block';

                    $letterContent.css({
                        'top': topValue,
                        'bottom': bottomValue,
                        'left': leftValue,
                        'right': rightValue
                    });
                } else {
                    $letterContent.css(initialStyles);
                }
            }

            updateStyles();

            $topInput.on('input', updateStyles);
            $bottomInput.on('input', updateStyles);
            $leftInput.on('input', updateStyles);
            $rightInput.on('input', updateStyles);

            $customStyle.on('change', updateStyles);

            var buttons = document.querySelectorAll('.btn-outline-secondary');

            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var target = this.getAttribute('data-target');
                    var delta = parseInt(this.getAttribute('data-delta'));
                    changeValue(target, delta);
                    updateStyles();

                });
            });

            function changeValue(id, delta) {
                var input = document.getElementById(id);
                var value = parseInt(input.value) || 0;
                input.value = value + delta;
            }


        });

        const nepaliTypeCheckbox = document.getElementById('CustomStyle');
        const customStyleInputs = document.getElementById('custom-style-inputs');

        nepaliTypeCheckbox.addEventListener('change', function() {
            if (nepaliTypeCheckbox.checked) {
                customStyleInputs.style.display = 'block';

            } else {
                customStyleInputs.style.display = 'none';
            }
        });

    });
})(window.jQuery);