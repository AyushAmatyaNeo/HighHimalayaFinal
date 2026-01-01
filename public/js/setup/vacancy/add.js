(function () {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var summernotes = [];
        
        $('.summernote')
                .each(function () {
                    var $this = $(this);
                    var temp = decodeURIComponent($this.parent().find("input[name='description']").val());
                    $this.summernote({height: 300,
                        minHeight: null,
                        maxHeight: null,
                        focus: true
                    });
                    $this.summernote('code', temp);
                    summernotes.push($this);
                });
        $('#VacancyForm').each(function () {
            var $this = $(this);
            $this.submit(function (e) {
                $this = $(this);
                var summernote = $this.find('.summernote');
                var message = $this.find("input[name='description']");
                $(message).val(summernote.summernote('code'));
                // form validation start
                if (message.val() == "" || message.val() == " ") {
                    var parentId = message.parent(".form-group");
                    var errorMsgSpan = parentId.find('span.errorMsg');
                    console.log(errorMsgSpan.length);
                    if (errorMsgSpan.length == 0) {
                        var errorMsgSpan = $('<span />', {
                            "class": 'errorMsg',
                            text: 'Message body cant be Empty'
                        });
                        parentId.append(errorMsgSpan);
                        message.focus();
                    }
                    return false;
                }
                // form validation end
                return true;
            });
        });
    });
})(window.jQuery, window.app);