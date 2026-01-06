<?php
namespace MarkNovel\Base;

class PostTypes {
    public function register() {
        // 1. TẠO POST TYPE: TRUYỆN (NOVEL)
        register_post_type('novel', [
            'labels' => [
                'name'          => 'Quản Lý Truyện',
                'singular_name' => 'Truyện',
                'add_new'       => 'Thêm Truyện Mới',
                'add_new_item'  => 'Thêm Truyện Mới',
                'edit_item'     => 'Sửa Truyện',
                'new_item'      => 'Truyện Mới',
                'view_item'     => 'Xem Truyện',
                'search_items'  => 'Tìm kiếm truyện',
                'not_found'     => 'Không tìm thấy truyện',
                'all_items'     => 'Tất cả truyện'
            ],
            'public'      => true,
            'has_archive' => true,
            'menu_icon'   => 'dashicons-book',
            // Truyện là cha, nên thường không cần hierarchical, nhưng cần thumbnail, excerpt
            'supports'    => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'],
            'rewrite'     => ['slug' => 'truyen'],
        ]);

        // 2. TẠO POST TYPE: CHƯƠNG (CHAPTER)
        register_post_type('chapter', [
            'labels' => [
                'name'          => 'Quản Lý Chương',
                'singular_name' => 'Chương',
                'add_new'       => 'Thêm Chương Mới',
                'add_new_item'  => 'Thêm Chương Mới',
                'edit_item'     => 'Sửa Chương',
                'all_items'     => 'Tất cả chương'
            ],
            'public'       => true,
            'show_ui'      => true,
            'show_in_menu' => 'edit.php?post_type=novel', // Hiển thị trong menu con của Truyện cho gọn
            
            // --- CẤU HÌNH QUAN TRỌNG ĐỂ CÓ MỤC CHỌN TRUYỆN CHA ---
            'hierarchical' => true,  // BẮT BUỘC: Để có quan hệ cha-con
            'supports'     => ['title', 'editor', 'page-attributes', 'comments', 'author'], // BẮT BUỘC: 'page-attributes' để hiện ô chọn Parent
            // -----------------------------------------------------
            
            'rewrite'      => ['slug' => 'chuong'],
        ]);
    }
}