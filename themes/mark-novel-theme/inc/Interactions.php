<?php
namespace MarkNovel\Features;

class Interactions {
    public function register() {
        // 1. Tăng View
        add_action('wp_ajax_mark_update_view', [$this, 'update_view']);
        add_action('wp_ajax_nopriv_mark_update_view', [$this, 'update_view']);

        // 2. Báo lỗi
        add_action('wp_ajax_mark_report_error', [$this, 'handle_report']);
        add_action('wp_ajax_nopriv_mark_report_error', [$this, 'handle_report']);
        add_action('init', [$this, 'register_report_cpt']);

        // 3. Like truyện
        add_action('wp_ajax_mark_like_novel', [$this, 'handle_like']);       // <--- Trùng khớp
        add_action('wp_ajax_nopriv_mark_like_novel', [$this, 'handle_like']); // <--- Trùng khớp

        // 3. Shortcode Tủ Truyện
        add_shortcode('mark_bookmarks', [$this, 'render_bookmark_list']);
    }

    // --- LOGIC VIEW ---
    public function update_view() {
        $post_id = intval($_POST['post_id']);
        if (!$post_id) wp_send_json_error();

        // Tăng view cho Chapter/Truyện hiện tại
        $views = (int) get_post_meta($post_id, 'post_views_count', true);
        $views++;
        update_post_meta($post_id, 'post_views_count', $views);

        // Nếu là Chapter, tăng view cho Truyện Cha luôn
        if (get_post_type($post_id) === 'chapter') {
            $parent_id = get_post_meta($post_id, '_parent_novel_id', true);
            if ($parent_id) {
                $parent_views = (int) get_post_meta($parent_id, 'post_views_count', true);
                $parent_views++;
                update_post_meta($parent_id, 'post_views_count', $parent_views);
            }
        }

        wp_send_json_success(['views' => number_format_i18n($views)]);
    }

    // --- LOGIC BÁO LỖI (Lưu vào CPT Report để Admin quản lý) ---
    public function register_report_cpt() {
        register_post_type('report', [
            'labels' => ['name' => 'Báo Lỗi', 'singular_name' => 'Báo Lỗi'],
            'public' => false,  // Không hiện ra ngoài web
            'show_ui' => true,  // Hiện trong Admin để quản lý
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_icon' => 'dashicons-warning',
        ]);
    }

    public function handle_report() {
        $chapter_id = intval($_POST['post_id']);
        $content = sanitize_textarea_field($_POST['content']);
        
        if (!$chapter_id || empty($content)) wp_send_json_error('Thiếu thông tin');

        $chapter_title = get_the_title($chapter_id);
        
        // Tạo bài post báo lỗi mới
        wp_insert_post([
            'post_type' => 'report',
            'post_title' => 'Báo lỗi: ' . $chapter_title,
            'post_content' => "Nội dung lỗi: " . $content . "\n\nLink: " . get_permalink($chapter_id),
            'post_status' => 'publish' // Để Admin thấy ngay
        ]);

        wp_send_json_success('Đã gửi báo lỗi thành công!');
    }

    // --- LOGIC LIKE (MỚI) ---
    public function handle_like() {
        $post_id = intval($_POST['post_id']);
        if (!$post_id) wp_send_json_error();

        $likes = (int) get_post_meta($post_id, 'post_like_count', true);
        $likes++;
        update_post_meta($post_id, 'post_like_count', $likes);

        wp_send_json_success(['likes' => number_format_i18n($likes)]);
    }

    // --- LOGIC TỦ TRUYỆN (HTML cho JS điền dữ liệu vào) ---
    public function render_bookmark_list() {
        return '<div id="mark-bookmark-list" class="novel-grid">Đang tải tủ truyện...</div>';
    }
}