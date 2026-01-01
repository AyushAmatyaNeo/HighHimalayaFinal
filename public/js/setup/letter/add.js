(function($) {
    'use strict';
    $(document).ready(function() {
        $('select').select2();

        let editorCount = 1;
        var focusedEditor = null;
        initializeEditor(0);

        $('#addPage').click(function(e) {
            e.preventDefault();
            initializeEditor(editorCount);
            editorCount++;
        });

        function initializeEditor(index) {
            var imageUrl = $('#ImageUrl').val();
            var newEditorHtml = `
                <div class="form-group" id="page-${index}">
                    <label class="control-label">Page ${index + 1} </label> 
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


            var searchUrl = document.searchurl;

            $(`#summernote-${index}`).summernote({
                height: 800,
                minHeight: null,
                maxHeight: null,
                focus: true,

            }).on('summernote.change', function() {
                updateRenderedContent(index);
            });

            var defaultContent = $(`#description-${index}`).val();
            $(`#summernote-${index}`).summernote('code', defaultContent);

            updateRenderedContent(index);

            $('.variables').each(function() {
                var $this = $(this);
                $this.off('click').on('click', function() {
                    var variable = $this.data('variable').trim();
                    $(`#summernote-${index}`).summernote('insertText', '[' + variable + ']');
                });
            });
            // var uniqueIds = [];
            // $('.custom_variables').each(function() {
            //     var $this = $(this);
            //     $this.off('click').on('click', function() {
            //         var variable = $this.data('variable');
            //         var custom_id = $this.data('id');

            //         if (uniqueIds.indexOf(custom_id) === -1) {
            //             uniqueIds.push(custom_id);
            //             $('#customVariables').val(uniqueIds.join(','));
            //         }
            //         console.log(uniqueIds);
            //         $('#summernote-' + index).summernote('insertText', '[' + variable + ']');
            //     });
            // });

        }

        function updateRenderedContent(index) {
            var content = $(`#summernote-${index}`).summernote('code');
            $(`#rendered-content-${index}`).html(content);
        }

        $('#nepaliType').change(function() {
            if ($(this).is(':checked')) {
                $('#language-textarea').show();
                $('#keyboard').show();
            } else {
                $('#language-textarea').hide();
                $('#keyboard').hide();
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

        function changeValue(id, delta) {
            var input = document.getElementById(id);
            var value = parseInt(input.value) || 0;
            input.value = value + delta;
        }

        document.querySelectorAll('.btn-outline-secondary').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                var delta = parseInt(this.getAttribute('data-delta'));
                changeValue(target, delta);
                updateStyles();
            });
        });

        $('#CustomStyle').change(function() {
            $('#custom-style-inputs').toggle(this.checked);
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


    });
})(window.jQuery);