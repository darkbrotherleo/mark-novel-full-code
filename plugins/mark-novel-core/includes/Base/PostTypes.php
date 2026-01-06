<?php
namespace MarkNovel\Base;

class PostTypes {
    public function register() {
        // Tạo Truyện
        register_post_type('novel', [
            'labels' => ['name' => 'Quản Lý Truyện', 'singular_name' => 'Truyện'],
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-book',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite' => ['slug' => 'truyen'],
        ]);

        // Tạo Chương
        register_post_type('chapter', [
            'labels' => ['name' => 'Quản Lý Chương', 'singular_name' => 'Chương', 'add_new' => 'Thêm Chương Mới'],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=novel', // Nhét vào menu con của Truyện
            'supports' => ['title', 'editor'],
            'rewrite' => ['slug' => 'chuong'],
        ]);
    }
}