<?php
namespace MarkNovel\Base;

class Taxonomies {
    public function register() {
        
        // 1. THỂ LOẠI (Có phân cấp - Giống Categories)
        register_taxonomy('novel_genre', 'novel', [
            'labels' => [
                'name'              => 'Thể Loại',
                'singular_name'     => 'Thể Loại',
                'search_items'      => 'Tìm kiếm thể loại',
                'all_items'         => 'Tất cả thể loại',
                'parent_item'       => 'Thể loại cha',
                'parent_item_colon' => 'Thể loại cha:',
                'edit_item'         => 'Sửa thể loại',
                'update_item'       => 'Cập nhật thể loại',
                'add_new_item'      => 'Thêm thể loại mới',
                'new_item_name'     => 'Tên thể loại mới',
                'menu_name'         => 'Thể Loại',
            ],
            'hierarchical'      => true,  // Kiểu checkbox cha/con
            'public'            => true,  // Cho phép truy vấn công khai
            'show_ui'           => true,  // Hiển thị trong Admin
            'show_admin_column' => true,  // Hiển thị cột trong danh sách truyện
            'show_in_nav_menus' => true,  // Cho phép thêm vào Menu
            'show_in_rest'      => true,  // [QUAN TRỌNG] Bắt buộc để Block Editor lưu được dữ liệu
            'rewrite'           => ['slug' => 'the-loai', 'with_front' => true],
        ]);

        // 2. TÁC GIẢ (Dạng thẻ Tag - Không phân cấp)
        // Lưu ý: Chỉ nên gán cho 'novel', không cần gán cho 'chapter' để tránh rác database
        register_taxonomy('novel_author', 'novel', [
            'labels' => [
                'name'                       => 'Tác Giả',
                'singular_name'              => 'Tác Giả',
                'search_items'               => 'Tìm tác giả',
                'popular_items'              => 'Tác giả phổ biến',
                'all_items'                  => 'Tất cả tác giả',
                'edit_item'                  => 'Sửa tác giả',
                'update_item'                => 'Cập nhật tác giả',
                'add_new_item'               => 'Thêm tác giả mới',
                'new_item_name'              => 'Tên tác giả mới',
                'separate_items_with_commas' => 'Ngăn cách các tác giả bằng dấu phẩy',
                'add_or_remove_items'        => 'Thêm hoặc xóa tác giả',
                'choose_from_most_used'      => 'Chọn từ tác giả dùng nhiều nhất',
                'menu_name'                  => 'Tác Giả',
            ],
            'hierarchical'      => false, // Kiểu tag (nhập text rồi enter)
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,  // [QUAN TRỌNG]
            'rewrite'           => ['slug' => 'tac-gia', 'with_front' => true],
        ]);

        // 3. TRẠNG THÁI (Full/Ongoing - Có phân cấp để chọn cho dễ)
        register_taxonomy('novel_status', 'novel', [
            'labels' => [
                'name'              => 'Trạng Thái',
                'singular_name'     => 'Trạng Thái',
                'search_items'      => 'Tìm trạng thái',
                'all_items'         => 'Tất cả trạng thái',
                'edit_item'         => 'Sửa trạng thái',
                'update_item'       => 'Cập nhật trạng thái',
                'add_new_item'      => 'Thêm trạng thái mới',
                'menu_name'         => 'Trạng Thái',
            ],
            'hierarchical'      => true, // Nên để true để hiện checkbox chọn cho nhanh (Full/Đang ra)
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true, // [QUAN TRỌNG]
            'rewrite'           => ['slug' => 'trang-thai', 'with_front' => true],
        ]);
    }
}