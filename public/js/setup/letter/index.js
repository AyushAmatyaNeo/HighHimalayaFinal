(function($) {
    'use strict';
    $(document).ready(function() {
        var $table = $('#letterTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:LETTER_SETUP_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var chilAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit btn-info" title="Child" href="' + document.subletter + '/#:LETTER_SETUP_ID#" style="height:17px;"> <i class="fa fa-child"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:LETTER_SETUP_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + chilAction + deleteAction;

        app.initializeKendoGrid($table, [
                { field: "LETTER_TITLE", title: "Letter Title", width: 550 },
                { field: "LETTER_SETUP_ID", title: "Action", width: 70, template: action },
            ],
            function(e) {
                app.serverRequest(document.childList, { letterId: e.data.LETTER_SETUP_ID }).then(function(response) {
                    console.log("Server response:", response);


                    $("<div/>").appendTo(e.detailCell).kendoGrid({
                        dataSource: {
                            data: response.data,
                            pageSize: 20
                        },
                        scrollable: false,
                        sortable: false,
                        pageable: false,
                        columns: [
                            { field: "LETTER_TITLE", title: "Sub Letters", width: 70 },
                        ],
                        dataBound: function(e) {
                            var grid = e.sender;
                            if (grid.dataSource.total() === 0) {
                                var colCount = grid.columns.length;
                                $(e.sender.wrapper)
                                    .find('tbody')
                                    .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                            }
                        },
                    });
                }, function(error) {
                    console.error("Error fetching child grid data:", error);
                });
            }
        );

        app.searchTable('letterTable', ['LETTER_TITLE', 'LETTER_SETUP_ID']);

        app.pullDataById("", {}).then(function(response) {
            app.renderKendoGrid($table, response.data);
        }, function(error) {
            console.error("Error rendering initial grid:", error);
        });
        var $logo = $("#form-logo");
        var $myAwesomeDropzone = $('#my-awesome-dropzone');
        var $uploadedImage = $('#uploadedImage');

        var imageData = {
            fileCode: null,
            fileName: null,
            oldFileName: null
        };
        if (typeof document.imageData !== 'undefined' && document.imageData != null) {
            imageData = document.imageData
        }

        var toggle = function() {
            if (imageData.fileName == null) {
                $myAwesomeDropzone.show();
                $uploadedImage.hide();
                $('#uploadFile').text("Upload");
            } else {
                console.log('here');
                $($uploadedImage.children()[0]).attr('src', document.basePath + "/uploads/" + imageData.fileName);
                $logo.val(imageData.fileCode);
                $myAwesomeDropzone.hide();
                $uploadedImage.show();
                $('#uploadFile').text("Edit");
            }
        }
        toggle();

        var dropZone = null;

        Dropzone.options.myAwesomeDropzone = {
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png",  // Only allow JPG and PNG files
            autoProcessQueue: false,
            addRemoveLinks: true,
            init: function() {
                dropZone = this;
                this.on('success', function(file, success) {
                    imageData = success.data;
                    $logo.val(imageData.fileCode);
                    toggle();
                });
                this.on("error", function(file, message) {
                    $('#InvalidFile').show();
                    this.removeFile(file);
                });
                this.on("addedfile", function() {

                    $('#InvalidFile').hide();
                    $('#EmptyFile').hide();
                });
            }
        };

        $('#uploadFile').on('click', function() {
            if ($(this).text() == "Edit") {
                imageData.fileName = null;
                toggle();
            } else {

                if (dropZone.files.length == 0) {
                    $('#EmptyFile').show();
                }
                dropZone.processQueue();
            }
        });
    });


})(window.jQuery);