(function($) {
    'use strict';
    $(document).ready(function() {
        var $table = $('#subletterTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:LETTER_SETUP_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:LETTER_SETUP_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;

        app.initializeKendoGrid($table, [
            { field: "LETTER_TITLE", title: "Letter Title", width: 70 },
            { field: "LETTER_SETUP_ID", title: "Action", width: 70, template: action }
        ], null, null, null, 'Letter List');

        app.searchTable('subletterTable', ['LETTER_TITLE', 'LETTER_SETUP_ID']);

        app.pullDataById("", {}).then(function(response) {
            app.renderKendoGrid($table, response.data);
        }, function(error) {});
    });
})(window.jQuery);