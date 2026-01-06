jQuery(document).ready(function($){
    // Xử lý khi bấm nút Upload
    $('.mark-upload-btn').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var targetInput = button.prev('input'); // Ô input nằm ngay trước nút bấm

        // Mở khung Media của WP
        var custom_uploader = wp.media({
            title: 'Chọn Ảnh',
            button: { text: 'Sử dụng ảnh này' },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            targetInput.val(attachment.url); // Gán link ảnh vào ô input
            
            // Nếu có thẻ img preview thì cập nhật luôn
            var preview = button.closest('td').find('.mark-img-preview');
            if(preview.length) {
                preview.attr('src', attachment.url).show();
            }
        }).open();
    });
});