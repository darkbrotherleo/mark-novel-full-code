<?php
/**
 * Plugin Name: Mark Novel Core
 * Description: Hệ thống quản lý Truyện - Chương chuyên nghiệp.
 * Version: 1.0.0
 * Author: Mark Dev
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Chặn truy cập trực tiếp

// 1. Định nghĩa đường dẫn
define( 'MARK_NOVEL_PATH', plugin_dir_path( __FILE__ ) );
define( 'MARK_NOVEL_URL', plugin_dir_url( __FILE__ ) );

// 2. Nhúng các file logic (Include)
require_once MARK_NOVEL_PATH . 'includes/Base/PostTypes.php';
require_once MARK_NOVEL_PATH . 'includes/Base/Taxonomies.php';
require_once MARK_NOVEL_PATH . 'includes/Admin/MetaBoxes.php';
require_once MARK_NOVEL_PATH . 'includes/Model/DataIntegrity.php';
require_once MARK_NOVEL_PATH . 'includes/Frontend/TemplateLoader.php'; // MỚI

use MarkNovel\Frontend\TemplateLoader; // MỚI

// 3. Sử dụng Namespace
use MarkNovel\Base\PostTypes;
use MarkNovel\Base\Taxonomies;
use MarkNovel\Admin\MetaBoxes;
use MarkNovel\Model\DataIntegrity;

// 4. Hàm khởi chạy chính
function mark_novel_init() {
    // Khởi tạo Post Types (Truyện, Chương)
    $post_types = new PostTypes();
    $post_types->register();

    // Khởi tạo Taxonomies (Thể loại, Tác giả...)
    $taxonomies = new Taxonomies();
    $taxonomies->register();

    // Khởi tạo tính năng tự động xóa
    $integrity = new DataIntegrity();
    $integrity->register();

    // Khởi chạy Template Loader (MỚI)
    $template_loader = new TemplateLoader();
    $template_loader->register();
}
add_action( 'init', 'mark_novel_init' );

// 5. Hàm khởi chạy cho trang Admin
function mark_novel_admin_init() {
    $meta_boxes = new MetaBoxes();
    $meta_boxes->register();
}
add_action( 'admin_init', 'mark_novel_admin_init' );

// 6. Nhúng JS/CSS cho Admin
function mark_novel_enqueue_admin($hook) {
    global $post;
    // Chỉ chạy ở trang thêm/sửa Chương
    if ( ($hook == 'post-new.php' || $hook == 'post.php') && 'chapter' === $post->post_type ) {
        wp_enqueue_script( 'mark-admin-js', MARK_NOVEL_URL . 'assets/admin/script.js', ['jquery'], '1.0', true );
        wp_enqueue_style( 'mark-admin-css', MARK_NOVEL_URL . 'assets/admin/style.css' );
    }
}
add_action( 'admin_enqueue_scripts', 'mark_novel_enqueue_admin' );

// 7. Nhúng CSS cho Frontend (Người xem)
function mark_novel_enqueue_frontend() {
    // Chỉ tải CSS khi đang xem Truyện hoặc Chương (Tối ưu tốc độ)
    if ( is_singular('novel') || is_singular('chapter') ) {
        wp_enqueue_style( 
            'mark-novel-style', // Tên handle (duy nhất)
            MARK_NOVEL_URL . 'assets/frontend/style.css', // Đường dẫn file
            [], 
            '1.0.0' 
        );
    }
}
add_action( 'wp_enqueue_scripts', 'mark_novel_enqueue_frontend' );