jQuery(document).ready(function($) {
    let timer;
    const input = $('#mark_novel_search');
    const results = $('#mark_search_results');
    const hiddenId = $('#mark_novel_id');

    input.on('keyup', function() {
        clearTimeout(timer);
        let term = $(this).val();
        
        if (term.length < 2) { results.hide(); return; }

        timer = setTimeout(function() {
            $.get(ajaxurl, { action: 'mark_search_novel', term: term }, function(res) {
                if (res.success && res.data.length) {
                    let html = '';
                    res.data.forEach(item => {
                        html += `<div class="mark-result-item" data-id="${item.id}">${item.title}</div>`;
                    });
                    results.html(html).show();
                } else {
                    results.html('<div style="padding:5px">Không tìm thấy!</div>').show();
                }
            });
        }, 500); // Đợi 0.5s sau khi gõ mới tìm
    });

    // Sự kiện click chọn
    $(document).on('click', '.mark-result-item', function() {
        input.val($(this).text());
        hiddenId.val($(this).data('id'));
        results.hide();
    });
});