(function ($) {
    "use strict";

    var fileFrame = null;
    var metaBox = $("div[id^='thumb_info_']"),
        addImgLink = metaBox.find('.upload-custom-img'),
        imgContainer = metaBox.find( '.custom-img-container');

    $(function() {
        $(".upload-custom-img").on("click", showMediaUploader);

        // hack for added fields
        $('.input_fields_wrap').on('click', '.upload-custom-img.new', showMediaUploader);
    });

    function showMediaUploader(e) {
        e.preventDefault();
        var self = this;

            fileFrame = wp.media.frames.file_frame = wp.media({
                title: screenHelp.title,
                button: {
                    text: screenHelp.buttomText
                },
                library: {
                    type: 'image' // limits the frame to show only images
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });

           // fileFrame.state().get('selection').toJSON();
            fileFrame.on("select", function() {

                setCustomImage(self);
                fileFrame.close();
            });

            fileFrame.open();
    }

    function setCustomImage(btn) {

        var attachment = fileFrame.state().get("selection").first().toJSON();

        console.log($(btn).prev());
        $(btn).prev().val(attachment.url);

        $(btn).prevAll('img').first().attr('src', attachment.url);
        $(btn).prevAll('img').first().toggleClass('hidden', 'visible');

        $(btn).val(screenHelp.uploadButtonText);

    }


})(jQuery);
