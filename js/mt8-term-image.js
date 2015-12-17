jQuery(document).ready(function($){
    var custom_uploader;
    $('#mt8-term-image-up').click(function(e) {
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        } 
        custom_uploader = wp.media({
            library: {
                type: 'image'
            }, 
            multiple: false
        }); 
        custom_uploader.on('select', function() {
            var images = custom_uploader.state().get('selection');
            images.each(function(file){
                $('#mt8-term-image').append('<img src="'+file.toJSON().url+'" />');
                $('#mt8-term-image-inp').val(file.toJSON().id);				
            });
        }); 
        custom_uploader.open();
    }); 
});