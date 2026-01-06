<?php
/**
 * Theme Options V2.0: Tabs, Custom Logo, Submenu
 */

// 1. Đăng ký Menu con trong Appearance (Themes)
function mark_theme_options_menu() {
    add_theme_page(
        'Cấu Hình Mark Novel',    // Page Title
        'Cấu Hình Theme',         // Menu Title
        'manage_options',         // Quyền hạn
        'mark-theme-options',     // Slug
        'mark_theme_options_page_html' // Hàm hiển thị
    );
}
add_action( 'admin_menu', 'mark_theme_options_menu' );

// 2. Đăng ký Settings
function mark_theme_options_init() {
    register_setting( 'mark_theme_options_group', 'mark_theme_options', 'mark_options_merge_sanitize' );

    // --- TAB 1: CHUNG (Logo, Favicon) ---
    add_settings_section('mark_general_section', '', null, 'mark-theme-options-general');
    
    add_settings_field('mark_logo', 'Logo Website', 'mark_image_render', 'mark-theme-options-general', 'mark_general_section', ['key' => 'logo_url']);
    add_settings_field('mark_favicon', 'Favicon (Icon Tab)', 'mark_image_render', 'mark-theme-options-general', 'mark_general_section', ['key' => 'favicon_url']);

    // --- TAB 2: FOOTER (Bản quyền) ---
    add_settings_section('mark_footer_section', '', null, 'mark-theme-options-footer');
    
    add_settings_field('mark_copyright', 'Nội dung bản quyền', 'mark_text_render', 'mark-theme-options-footer', 'mark_footer_section', ['key' => 'copyright_text']);

    // --- TAB 3: SCRIPTS (Chèn Code) ---
    add_settings_section('mark_scripts_section', '', null, 'mark-theme-options-scripts');

    add_settings_field('mark_header_code', 'Header Code (<head>)', 'mark_textarea_render', 'mark-theme-options-scripts', 'mark_scripts_section', ['key' => 'header_code']);
    add_settings_field('mark_body_code', 'Body Code (<body>)', 'mark_textarea_render', 'mark-theme-options-scripts', 'mark_scripts_section', ['key' => 'body_code']);
    add_settings_field('mark_footer_code', 'Footer Code (<footer>)', 'mark_textarea_render', 'mark-theme-options-scripts', 'mark_scripts_section', ['key' => 'footer_code']);
}
add_action( 'admin_init', 'mark_theme_options_init' );

/**
 * HÀM XỬ LÝ QUAN TRỌNG:
 * Giúp trộn dữ liệu mới vào dữ liệu cũ để không bị mất settings ở các Tab khác
 */
function mark_options_merge_sanitize( $input ) {
    // 1. Lấy dữ liệu cũ đang có trong Database
    $old_options = get_option( 'mark_theme_options' );
    
    // Nếu chưa có dữ liệu cũ (lần đầu lưu) thì gán mảng rỗng
    if ( ! is_array( $old_options ) ) {
        $old_options = [];
    }

    // 2. Trộn dữ liệu mới ($input) đè lên dữ liệu cũ ($old_options)
    // array_merge sẽ giữ nguyên các key cũ không có trong input, và cập nhật các key mới
    $output = array_merge( $old_options, $input );

    return $output;
}

// --- CÁC HÀM RENDER FIELD ---

// Field chọn ảnh (Logo/Favicon)
function mark_image_render($args) {
    $options = get_option('mark_theme_options');
    $key = $args['key'];
    $value = isset($options[$key]) ? $options[$key] : '';
    ?>
    <div style="display:flex; align-items:center; gap:10px;">
        <input type="text" name="mark_theme_options[<?php echo $key; ?>]" value="<?php echo esc_attr($value); ?>" style="width: 300px;" />
        <button class="button mark-upload-btn">Chọn Ảnh</button>
    </div>
    <br>
    <img class="mark-img-preview" src="<?php echo esc_attr($value); ?>" style="max-height: 80px; display: <?php echo $value ? 'block' : 'none'; ?>; border:1px solid #ddd; padding:3px;" />
    <?php
}

// Field nhập Text thường
function mark_text_render($args) {
    $options = get_option('mark_theme_options');
    $key = $args['key'];
    $value = isset($options[$key]) ? $options[$key] : '';
    echo '<input type="text" name="mark_theme_options['.$key.']" value="' . esc_attr($value) . '" style="width: 100%; max-width: 500px;" />';
}

// Field nhập Textarea (Code)
function mark_textarea_render($args) {
    $options = get_option('mark_theme_options');
    $key = $args['key'];
    $value = isset($options[$key]) ? $options[$key] : '';
    echo '<textarea name="mark_theme_options['.$key.']" rows="6" style="width:100%; font-family:monospace; background:#f9f9f9;">' . esc_textarea($value) . '</textarea>';
}

// --- GIAO DIỆN TRANG ADMIN (CÓ TABS) ---
function mark_theme_options_page_html() {
    if (!current_user_can('manage_options')) return;
    
    // Xác định Tab hiện tại
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
    ?>
    <div class="wrap">
        <h1>⚙️ Cấu Hình Theme Mark Novel</h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=mark-theme-options&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">Chung (Logo)</a>
            <a href="?page=mark-theme-options&tab=footer" class="nav-tab <?php echo $active_tab == 'footer' ? 'nav-tab-active' : ''; ?>">Footer</a>
            <a href="?page=mark-theme-options&tab=scripts" class="nav-tab <?php echo $active_tab == 'scripts' ? 'nav-tab-active' : ''; ?>">Chèn Scripts</a>
        </h2>

        <form action="options.php" method="post">
            <?php
            settings_fields('mark_theme_options_group');
            
            // Hiển thị nội dung theo Tab
            if ($active_tab == 'general') {
                do_settings_sections('mark-theme-options-general');
            } elseif ($active_tab == 'footer') {
                do_settings_sections('mark-theme-options-footer');
            } elseif ($active_tab == 'scripts') {
                do_settings_sections('mark-theme-options-scripts');
            }
            
            submit_button('Lưu Thay Đổi');
            ?>
        </form>
    </div>
    <?php
}