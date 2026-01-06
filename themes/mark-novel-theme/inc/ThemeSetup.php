<?php
namespace MarkNovel\Core;

class ThemeSetup {
    public function register() {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function setup() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('custom-logo');

        // Đăng ký Menu
        register_nav_menus([
            'primary' => 'Menu Chính (Header)'
        ]);
    }

    public function enqueue() {
        // CSS
        wp_enqueue_style('mark-main-style', get_template_directory_uri() . '/assets/css/main.css', [], '3.5');

        // JS
        wp_enqueue_script('mark-frontend-js', get_template_directory_uri() . '/assets/js/frontend.js', ['jquery'], '3.5', true);

        // Truyền biến cho JS (Ajax)
        wp_localize_script('mark-frontend-js', 'mark_ajax', [
            'url' => admin_url('admin-ajax.php'),
            'is_logged_in' => is_user_logged_in(),
            'nonce' => wp_create_nonce('mark_frontend_nonce')
        ]);
    }
}