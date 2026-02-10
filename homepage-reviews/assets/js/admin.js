jQuery(document).ready(function($){
    var frame;
    var currentButton;
    var currentIndex;

    // Handle Upload Image
    $('.upload-gallery-image').on('click', function(e){
        e.preventDefault();
        currentButton = $(this);
        currentIndex = currentButton.data('index');

        // If the media frame already exists, reopen it.
        if ( frame ) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select Gallery Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // When an image is selected in the media frame...
        frame.on( 'select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            
            // Set the value of the hidden input
            $('#review_gallery_image_' + currentIndex).val(attachment.id);
            
            // Update the preview
            var previewContainer = $('.image-preview-' + currentIndex);
            previewContainer.html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 100%; max-height: 100%;" />');
        });

        // Finally, open the modal on click
        frame.open();
    });

    // Handle Remove Image
    $('.remove-gallery-image').on('click', function(e){
        e.preventDefault();
        var index = $(this).data('index');
        
        // Clear hidden input
        $('#review_gallery_image_' + index).val('');
        
        // Clear preview
        $('.image-preview-' + index).html('<span>No Image</span>');
    });
});
