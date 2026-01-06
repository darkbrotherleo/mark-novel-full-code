<?php
namespace MarkNovel\Model;

class DataIntegrity {
    public function register() {
        add_action('before_delete_post', [$this, 'delete_related_chapters']);
    }

    public function delete_related_chapters($post_id) {
        if (get_post_type($post_id) !== 'novel') return;

        // Tìm các chương con
        $chapters = get_posts([
            'post_type' => 'chapter',
            'meta_key' => '_parent_novel_id',
            'meta_value' => $post_id,
            'numberposts' => -1
        ]);

        foreach ($chapters as $chapter) {
            wp_delete_post($chapter->ID, true); // Xóa vĩnh viễn
        }
    }
}