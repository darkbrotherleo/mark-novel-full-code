jQuery(document).ready(function($) {

    /* ==========================================================================
       0. TÍNH NĂNG USER: ĐỒNG BỘ DỮ LIỆU (AUTO SYNC)
       ========================================================================== */
    // Kiểm tra nếu người dùng đã đăng nhập (biến mark_ajax.is_logged_in được truyền từ PHP)
    if (typeof mark_ajax !== 'undefined' && mark_ajax.is_logged_in) {
        let localBookmarks = JSON.parse(localStorage.getItem('mark_bookmarks') || '[]');
        let localHistory   = JSON.parse(localStorage.getItem('mark_reading_history') || '[]');

        // Nếu dưới trình duyệt có dữ liệu cũ, đẩy ngay lên Server
        if (localBookmarks.length > 0 || localHistory.length > 0) {
            $.post(mark_ajax.url, {
                action: 'mark_sync_data',
                bookmarks: localBookmarks,
                history: localHistory
            }, function(res) {
                if (res.success) {
                    console.log('MarkNovel: Đã đồng bộ dữ liệu lên User Cloud!');
                    
                    // Xóa localStorage để tránh trùng lặp cho lần sau
                    localStorage.removeItem('mark_bookmarks');
                    localStorage.removeItem('mark_reading_history');
                    
                    // Nếu đang ở trang Tài khoản, reload để hiện dữ liệu mới
                    if (window.location.href.indexOf('tai-khoan') > -1) {
                        location.reload();
                    }
                }
            });
        }
    }
    
    /* ==========================================================================
       1. GIAO DIỆN & DARK MODE (UI/UX)
       ========================================================================== */
    
    // --- 1.1. Xử lý Dark Mode ---
    const themeBtn = $('#theme-toggle');
    if (themeBtn.length) {
        const currentTheme = localStorage.getItem('mark_theme');
        if (currentTheme === 'dark') {
            $('html').addClass('theme-dark');
        }

        themeBtn.on('click', function() {
            $('html').toggleClass('theme-dark');
            if ($('html').hasClass('theme-dark')) {
                localStorage.setItem('mark_theme', 'dark');
            } else {
                localStorage.setItem('mark_theme', 'light');
            }
        });
    }

    // --- 1.2. Xử lý Menu Mobile ---
    $('.menu-toggle').click(function() {
        $('.main-navigation').toggleClass('toggled');
        $(this).attr('aria-expanded', $(this).attr('aria-expanded') === 'true' ? 'false' : 'true');
    });

    /* ==========================================================================
       2. TÍNH NĂNG TỦ TRUYỆN (BOOKMARK) - [ĐÃ NÂNG CẤP]
       ========================================================================== */
    
    // --- 2.1. Kiểm tra trạng thái nút "Lưu Truyện" khi load trang ---
    function checkBookmarkStatus() {
        // Chỉ kiểm tra LocalStorage (cho khách). 
        // Với thành viên, PHP nên render class 'saved' sẵn từ server (nếu muốn tối ưu sau này).
        if (typeof mark_ajax === 'undefined' || !mark_ajax.is_logged_in) {
            $('.btn-bookmark').each(function() {
                const btn = $(this);
                const id = btn.data('id');
                let bookmarks = JSON.parse(localStorage.getItem('mark_bookmarks') || '[]');
                
                if (bookmarks.some(b => b.id === id)) {
                    btn.addClass('saved');
                    btn.html('<i class="dashicons dashicons-yes"></i> Đã Lưu (Bỏ?)');
                } else {
                    btn.removeClass('saved');
                    btn.html('<i class="dashicons dashicons-heart"></i> Lưu Truyện');
                }
            });
        }
    }
    checkBookmarkStatus();

    // --- 2.2. Xử lý sự kiện bấm nút Lưu (QUAN TRỌNG: PHÂN LUỒNG) ---
    $('.btn-bookmark').click(function(e) {
        e.preventDefault();
        const btn = $(this);
        
        // Dữ liệu truyện
        const novelData = {
            id: btn.data('id'),
            title: btn.data('title'),
            url: btn.data('url'),
            thumb: btn.data('thumb'),
            chapter: btn.data('chapter') || ''
        };

        // LUỒNG 1: ĐÃ ĐĂNG NHẬP -> GỬI AJAX LÊN SERVER
        if (typeof mark_ajax !== 'undefined' && mark_ajax.is_logged_in) {
            // Hiệu ứng chờ
            btn.css('opacity', '0.7');
            
            $.post(mark_ajax.url, {
                action: 'mark_user_action',
                type: 'bookmark',
                data: novelData
            }, function(res) {
                btn.css('opacity', '1');
                if (res.success) {
                    if (res.data.status === 'added') {
                        btn.addClass('saved');
                        btn.html('<i class="dashicons dashicons-yes"></i> Đã Lưu (Bỏ?)');
                    } else {
                        btn.removeClass('saved');
                        btn.html('<i class="dashicons dashicons-heart"></i> Lưu Truyện');
                    }
                } else {
                    alert('Lỗi kết nối server!');
                }
            });
        } 
        // LUỒNG 2: KHÁCH VÃNG LAI -> LƯU LOCALSTORAGE
        else {
            let bookmarks = JSON.parse(localStorage.getItem('mark_bookmarks') || '[]');
            const index = bookmarks.findIndex(b => b.id === novelData.id);

            if (index !== -1) {
                // Xóa
                bookmarks.splice(index, 1);
                localStorage.setItem('mark_bookmarks', JSON.stringify(bookmarks));
                btn.removeClass('saved');
                btn.html('<i class="dashicons dashicons-heart"></i> Lưu Truyện');
            } else {
                // Lưu
                bookmarks.push(novelData);
                localStorage.setItem('mark_bookmarks', JSON.stringify(bookmarks));
                btn.addClass('saved');
                btn.html('<i class="dashicons dashicons-yes"></i> Đã Lưu (Bỏ?)');
            }
        }
    });

    // --- 2.3. Hiển thị danh sách tủ truyện (Dùng cho Shortcode [mark_bookmarks] cũ) ---
    // (Lưu ý: Trang Profile User dùng PHP render, đoạn này chỉ hỗ trợ khách xem trên page tĩnh)
    const bookmarkList = $('#mark-bookmark-list');
    if (bookmarkList.length) {
        let bookmarks = JSON.parse(localStorage.getItem('mark_bookmarks') || '[]');
        
        if (bookmarks.length === 0) {
            bookmarkList.html('<p style="text-align:center; padding:20px; color:#666;">Tủ truyện trống (Trên thiết bị này).</p>');
        } else {
            let html = '';
            bookmarks.forEach(b => {
                html += `
                <div class="novel-card">
                    <a href="${b.url}" class="thumb-link">
                        <img src="${b.thumb}" style="width:100%; aspect-ratio:2/3; object-fit:cover;">
                    </a>
                    <div class="novel-body">
                        <h3><a href="${b.url}">${b.title}</a></h3>
                        <div class="novel-meta">
                            ${b.chapter ? '<span style="font-size:12px; color:var(--accent);">Đang đọc: ' + b.chapter + '</span>' : ''}
                            <button class="btn-remove-bookmark" data-id="${b.id}" style="color:red; border:none; background:none; cursor:pointer; font-size:12px; margin-top:5px;">[Xóa bỏ]</button>
                        </div>
                    </div>
                </div>`;
            });
            bookmarkList.html(html);
        }
    }
    
    // Nút xóa nhanh tại trang danh sách tủ truyện (localStorage)
    $(document).on('click', '.btn-remove-bookmark', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        let bookmarks = JSON.parse(localStorage.getItem('mark_bookmarks') || '[]');
        bookmarks = bookmarks.filter(b => b.id !== id);
        localStorage.setItem('mark_bookmarks', JSON.stringify(bookmarks));
        $(this).closest('.novel-card').fadeOut();
    });

    /* ==========================================================================
       3. TÍNH NĂNG THÍCH TRUYỆN (LIKE)
       ========================================================================== */
    $('.btn-like').click(function(e) {
        e.preventDefault();
        const btn = $(this);
        const postId = btn.data('id');
        
        // Kiểm tra LocalStorage (chặn spam click)
        const likedPosts = JSON.parse(localStorage.getItem('mark_liked_posts') || '[]');
        if (likedPosts.includes(postId)) {
            alert('Đạo hữu đã thích truyện này rồi!');
            return;
        }

        if (typeof mark_ajax !== 'undefined') {
            $.post(mark_ajax.url, {
                action: 'mark_like_novel',
                post_id: postId
            }, function(res) {
                if (res.success) {
                    btn.find('.like-count').text('(' + res.data.likes + ')');
                    btn.addClass('liked');
                    btn.find('.like-text').text('Đã Thích');
                    
                    likedPosts.push(postId);
                    localStorage.setItem('mark_liked_posts', JSON.stringify(likedPosts));
                }
            });
        }
    });

    // Check trạng thái Like khi load trang
    $('.btn-like').each(function() {
        const btn = $(this);
        const postId = btn.data('id');
        const likedPosts = JSON.parse(localStorage.getItem('mark_liked_posts') || '[]');
        if (likedPosts.includes(postId)) {
            btn.addClass('liked');
            btn.find('.like-text').text('Đã Thích');
        }
    });

    /* ==========================================================================
       4. TÍNH NĂNG LỊCH SỬ ĐỌC (HISTORY)
       ========================================================================== */
    // --- 4.1. Tự động ghi lịch sử ---
    const chapterData = $('#chapter-tracking-data'); // Thẻ ẩn lấy từ PHP
    
    if (chapterData.length) {
        const historyItem = {
            novelId: chapterData.data('novel-id'),
            novelTitle: chapterData.data('novel-title'),
            novelThumb: chapterData.data('novel-thumb'),
            chapterTitle: chapterData.data('chapter-title'),
            chapterUrl: chapterData.data('chapter-url'),
            time: new Date().getTime()
        };

        // LUỒNG 1: Đã đăng nhập -> Đẩy thẳng vào Database (thông qua sync sau này hoặc ajax realtime nếu muốn)
        // Hiện tại ta vẫn lưu vào localStorage trước, để hàm "Auto Sync" ở đầu file xử lý đẩy lên khi load trang sau.
        // Điều này giúp giảm request liên tục khi đọc truyện.
        
        let history = JSON.parse(localStorage.getItem('mark_reading_history') || '[]');
        history = history.filter(item => item.novelId !== historyItem.novelId); // Xóa cũ
        history.unshift(historyItem); // Thêm mới
        if (history.length > 12) history.pop(); // Giới hạn
        localStorage.setItem('mark_reading_history', JSON.stringify(history));
    }

    // --- 4.2. Hiển thị lịch sử đọc (Widget Sidebar - Khách) ---
    // (User đã đăng nhập sẽ xem trong trang Profile)
    const historyContainer = $('#mark-history-list'); 
    if (historyContainer.length) {
        let history = JSON.parse(localStorage.getItem('mark_reading_history') || '[]');
        if (history.length === 0) {
            historyContainer.html('<p class="text-muted" style="font-size:13px;">Chưa có lịch sử đọc truyện.</p>');
        } else {
            let html = '<ul class="history-list">';
            history.forEach(item => {
                html += `
                <li style="margin-bottom: 12px; border-bottom: 1px dashed var(--border); padding-bottom: 8px;">
                    <a href="${item.chapterUrl}" style="font-weight:bold; display:block; font-size:14px; margin-bottom:2px;">${item.novelTitle}</a>
                    <a href="${item.chapterUrl}" style="font-size:12px; color:var(--text-light);">
                        <i class="dashicons dashicons-redo"></i> Đọc tiếp: ${item.chapterTitle}
                    </a>
                </li>`;
            });
            html += '</ul>';
            historyContainer.html(html);
        }
    }

    /* ==========================================================================
       5. AJAX SYSTEM (VIEWS & REPORTS)
       ========================================================================== */
    
    // Tăng View
    if (typeof mark_ajax !== 'undefined' && mark_ajax.post_id) {
        setTimeout(function() {
            $.post(mark_ajax.url, {
                action: 'mark_update_view',
                post_id: mark_ajax.post_id
            });
        }, 3000);
    }

    // Báo lỗi
    $('.btn-report').click(function(e) { 
        e.preventDefault();
        $('.report-modal').fadeIn(); 
    });
    
    $('.close-modal').click(function() { 
        $('.report-modal').fadeOut(); 
    });

    $('#report-submit').click(function(e) {
        e.preventDefault();
        const content = $('#report-content').val();
        
        if(!content) { 
            alert('Đạo hữu chưa nhập nội dung lỗi!'); 
            return; 
        }
        
        const btn = $(this);
        btn.text('Đang gửi...').prop('disabled', true);

        $.post(mark_ajax.url, {
            action: 'mark_report_error',
            post_id: mark_ajax.post_id,
            content: content
        }, function(res) {
            btn.text('Gửi Báo Cáo').prop('disabled', false);
            if(res.success) {
                alert(res.data);
                $('.report-modal').fadeOut();
                $('#report-content').val('');
            } else {
                alert('Lỗi: ' + res.data);
            }
        });
    });

    /* ==========================================================================
       6. AUTH MODAL (ĐĂNG NHẬP / ĐĂNG KÝ)
       ========================================================================== */
    
    // Mở Modal Login
    $(document).on('click', '.js-open-login', function(e) {
        e.preventDefault();
        $('.mark-modal').fadeOut(); // Đóng các modal khác nếu có
        $('#modal-login').fadeIn().css('display', 'flex'); // Flex để căn giữa nếu cần CSS lại
    });

    // Mở Modal Register
    $(document).on('click', '.js-open-register', function(e) {
        e.preventDefault();
        $('.mark-modal').fadeOut();
        $('#modal-register').fadeIn();
    });

    // Đóng Modal (Nút X hoặc click ra ngoài)
    $('.modal-close, .modal-overlay').click(function() {
        $('.mark-modal').fadeOut();
    });

    // Chuyển đổi qua lại giữa Login <-> Register trong Modal
    $('.js-switch-to-register').click(function(e) {
        e.preventDefault();
        $('#modal-login').fadeOut(200, function() {
            $('#modal-register').fadeIn();
        });
    });

    $('.js-switch-to-login').click(function(e) {
        e.preventDefault();
        $('#modal-register').fadeOut(200, function() {
            $('#modal-login').fadeIn();
        });
    });

    // CSS lại display cho modal để căn giữa màn hình (Ghi đè fadeIn)
    // Thêm đoạn này vào CSS hoặc xử lý JS để modal đẹp hơn
    $('.mark-modal').css({
        'display': 'none',
        'align-items': 'center', 
        'justify-content': 'center'
    });
});