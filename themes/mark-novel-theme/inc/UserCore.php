<?php
namespace MarkNovel\Features;

class UserCore {
    public function register() {
        // 1. Shortcode
        add_shortcode('mark_login_form', [$this, 'render_login_form']);
        add_shortcode('mark_register_form', [$this, 'render_register_form']);

        // 2. Xử lý Form
        add_action('init', [$this, 'handle_user_actions']);

        // 3. Avatar
        add_filter('get_avatar_url', [$this, 'custom_avatar_url'], 10, 3);

        // 4. Ajax Sync
        add_action('wp_ajax_mark_sync_data', [$this, 'sync_user_data']);
        add_action('wp_ajax_mark_user_action', [$this, 'ajax_user_action']);
    }

    // --- FORM ĐĂNG NHẬP (ĐÃ VIỆT HÓA) ---
    public function render_login_form() {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            return '<p>Xin chào, <strong>' . esc_html($current_user->display_name) . '</strong>. Bạn đã đăng nhập.</p>';
        }

        // Cấu hình tiếng Việt cho Form đăng nhập
        $args = [
            'echo'           => false,
            'redirect'       => home_url('/tai-khoan/'), 
            'form_id'        => 'loginform',
            'label_username' => 'Tên đăng nhập hoặc Email',
            'label_password' => 'Mật khẩu',
            'label_remember' => 'Ghi nhớ đăng nhập',
            'label_log_in'   => 'Đăng Nhập',
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'wp-submit',
            'remember'       => true,
            'value_username' => '',
            'value_remember' => false
        ];

        return wp_login_form($args);
    }

    // --- FORM ĐĂNG KÝ (HTML TỰ VIẾT) ---
    public function render_register_form() {
        if (is_user_logged_in()) return '<p>Bạn đã đăng nhập!</p>';
        ob_start();
        ?>
        <form method="post" class="mark-auth-form">
            <p>
                <label for="mark_user_login">Tên tài khoản *</label>
                <input type="text" name="mark_user_login" id="mark_user_login" required placeholder="Nhập tên tài khoản...">
            </p>
            <p>
                <label for="mark_user_email">Email *</label>
                <input type="email" name="mark_user_email" id="mark_user_email" required placeholder="Nhập địa chỉ email...">
            </p>
            <p>
                <label for="mark_user_pass">Mật khẩu *</label>
                <input type="password" name="mark_user_pass" id="mark_user_pass" required placeholder="Nhập mật khẩu...">
            </p>
            
            <?php wp_nonce_field('mark_register_action', 'mark_register_nonce'); ?>
            
            <p style="margin-top:20px;">
                <button type="submit" name="mark_register_submit" class="btn-action">Đăng Ký Ngay</button>
            </p>
        </form>
        <?php
        return ob_get_clean();
    }

    // --- XỬ LÝ POST FORM (Đăng ký & Update Profile) ---
    public function handle_user_actions() {
        // Xử lý Đăng ký
        if (isset($_POST['mark_register_submit']) && wp_verify_nonce($_POST['mark_register_nonce'], 'mark_register_action')) {
            $user_login = sanitize_user($_POST['mark_user_login']);
            $user_email = sanitize_email($_POST['mark_user_email']);
            $user_pass  = $_POST['mark_user_pass'];

            $user_id = wp_create_user($user_login, $user_pass, $user_email);
            if (is_wp_error($user_id)) {
                // Hiển thị lỗi (bạn có thể style lại cho đẹp nếu muốn)
                echo '<div style="background:#ffcccc; color:red; padding:10px; text-align:center; border:1px solid red; margin-bottom:20px;">Lỗi: ' . $user_id->get_error_message() . '</div>';
            } else {
                // Tự động đăng nhập sau khi đăng ký thành công
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                wp_redirect(home_url('/tai-khoan/'));
                exit;
            }
        }

        // Xử lý Update Profile
        if (isset($_POST['mark_update_profile']) && is_user_logged_in()) {
            $user_id = get_current_user_id();
            
            // 1. Update Tên hiển thị
            if (!empty($_POST['display_name'])) {
                wp_update_user(['ID' => $user_id, 'display_name' => sanitize_text_field($_POST['display_name'])]);
            }

            // 2. Update Mật khẩu
            if (!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
                if ($_POST['pass1'] === $_POST['pass2']) {
                    wp_update_user(['ID' => $user_id, 'user_pass' => $_POST['pass1']]);
                }
            }

            // 3. Upload Avatar
            if (!empty($_FILES['user_avatar']['name'])) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $attachment_id = media_handle_upload('user_avatar', 0);
                if (!is_wp_error($attachment_id)) {
                    update_user_meta($user_id, '_mark_custom_avatar', $attachment_id);
                }
            }
        }
    }

    // --- CUSTOM AVATAR ---
    public function custom_avatar_url($url, $id_or_email, $args) {
        $user = false;
        if (is_numeric($id_or_email)) {
            $user = get_user_by('id', $id_or_email);
        } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
            $user = get_user_by('id', $id_or_email->user_id);
        }

        if ($user && is_object($user)) {
            $custom_avatar_id = get_user_meta($user->ID, '_mark_custom_avatar', true);
            if ($custom_avatar_id) {
                $img = wp_get_attachment_image_src($custom_avatar_id, 'thumbnail');
                if ($img) return $img[0];
            }
        }
        return $url;
    }

    // --- AJAX SYNC ---
    public function sync_user_data() {
        if (!is_user_logged_in()) wp_send_json_error();
        $user_id = get_current_user_id();

        if (isset($_POST['bookmarks'])) {
            update_user_meta($user_id, '_mark_user_bookmarks', $_POST['bookmarks']);
        }
        if (isset($_POST['history'])) {
            update_user_meta($user_id, '_mark_user_history', $_POST['history']);
        }
        wp_send_json_success('Synced');
    }

    // --- AJAX USER ACTION ---
    public function ajax_user_action() {
        if (!is_user_logged_in()) wp_send_json_error();
        $user_id = get_current_user_id();
        $type = $_POST['type']; 
        $data = $_POST['data']; 

        if ($type === 'bookmark') {
            $current = get_user_meta($user_id, '_mark_user_bookmarks', true) ?: [];
            $exists = false;
            foreach ($current as $k => $item) {
                if ($item['id'] == $data['id']) {
                    unset($current[$k]);
                    $exists = true;
                    break;
                }
            }
            if (!$exists) $current[] = $data;
            update_user_meta($user_id, '_mark_user_bookmarks', array_values($current));
            wp_send_json_success(['status' => $exists ? 'removed' : 'added']);
        }
    }
}