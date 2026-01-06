<?php
/**
 * Mark Novel Theme Functions
 */

// Định nghĩa đường dẫn
define('MARK_THEME_DIR', get_template_directory());
define('MARK_THEME_URI', get_template_directory_uri());

// 1. Nạp các file logic từ thư mục /inc/
require_once MARK_THEME_DIR . '/inc/ThemeSetup.php';
require_once MARK_THEME_DIR . '/inc/UserCore.php';
require_once MARK_THEME_DIR . '/inc/Interactions.php';

// 2. Khởi tạo các Class
function mark_theme_init() {
    // Setup Theme (CSS, JS, Support)
    $setup = new \MarkNovel\Core\ThemeSetup();
    $setup->register();

    // User System
    if (class_exists('\MarkNovel\Features\UserCore')) {
        $user = new \MarkNovel\Features\UserCore();
        $user->register();
    }

    // Interactions
    if (class_exists('\MarkNovel\Features\Interactions')) {
        $interact = new \MarkNovel\Features\Interactions();
        $interact->register();
    }
}
add_action('after_setup_theme', 'mark_theme_init');

// Sửa namespace cho ThemeSetup nếu cần khớp với file bạn tạo