<?php
namespace MarkNovel\Admin;

class MetaBoxes {
    public function register() {
        add_action('add_meta_boxes', [$this, 'add_boxes']);
        add_action('save_post', [$this, 'save_data']);
        add_action('wp_ajax_mark_search_novel', [$this, 'ajax_search']);
    }

    public function add_boxes() {
        // 1. Box chọn truyện cha (Dành cho CHƯƠNG)
        add_meta_box(
            'chapter_novel_link', 
            'Thuộc Truyện Nào?', 
            [$this, 'render_chapter_box'], 
            'chapter', 
            'side', 
            'high'
        );

        // 2. Box nhập nguồn (Dành cho TRUYỆN) - MỚI
        add_meta_box(
            'novel_source_info',
            'Thông Tin Bổ Sung',
            [$this, 'render_novel_box'],
            'novel',
            'side', // Để bên cột phải
            'default'
        );
    }

    // Render HTML cho trang CHƯƠNG
    public function render_chapter_box($post) {
        $novel_id = get_post_meta($post->ID, '_parent_novel_id', true);
        $novel_title = $novel_id ? get_the_title($novel_id) : '';

        wp_nonce_field('mark_save_meta', 'mark_nonce_check'); // Dùng chung 1 nonce
        echo '<input type="hidden" id="mark_novel_id" name="parent_novel_id" value="' . esc_attr($novel_id) . '">';
        echo '<input type="text" id="mark_novel_search" value="' . esc_attr($novel_title) . '" placeholder="Gõ tên truyện..." style="width:100%">';
        echo '<div id="mark_search_results" class="mark-results-box"></div>';
    }

    // Render HTML cho trang TRUYỆN (MỚI)
    public function render_novel_box($post) {
        // Lấy dữ liệu nguồn đã lưu
        $source = get_post_meta($post->ID, '_novel_source', true);
        
        wp_nonce_field('mark_save_meta', 'mark_nonce_check');
        
        echo '<label for="mark_novel_source" style="font-weight:bold; display:block; margin-bottom:5px;">Nguồn Truyện:</label>';
        echo '<input type="text" id="mark_novel_source" name="novel_source" value="' . esc_attr($source) . '" placeholder="VD: Tangthuvien, Tự sáng tác..." style="width:100%;">';
        echo '<p style="color:#666; font-size:12px; margin-top:5px;">Nhập tên nguồn hoặc link gốc.</p>';
    }

    // Lưu dữ liệu (Xử lý cả 2 loại)
    public function save_data($post_id) {
        if (!isset($_POST['mark_nonce_check']) || !wp_verify_nonce($_POST['mark_nonce_check'], 'mark_save_meta')) return;

        // 1. Lưu cho Chương
        if (isset($_POST['parent_novel_id'])) {
            update_post_meta($post_id, '_parent_novel_id', sanitize_text_field($_POST['parent_novel_id']));
        }

        // 2. Lưu cho Truyện (MỚI)
        if (isset($_POST['novel_source'])) {
            update_post_meta($post_id, '_novel_source', sanitize_text_field($_POST['novel_source']));
        }
    }

    // AJAX giữ nguyên
    public function ajax_search() {
        $term = sanitize_text_field($_GET['term']);
        $query = new \WP_Query(['post_type' => 'novel', 's' => $term, 'posts_per_page' => 5]);
        $results = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post(); $results[] = ['id' => get_the_ID(), 'title' => get_the_title()]; }
        }
        wp_send_json_success($results);
    }
}