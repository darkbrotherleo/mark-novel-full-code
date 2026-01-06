<?php $options = get_option( 'mark_theme_options' ); ?>

<footer class="site-footer">
    <div class="container">
        
        <div class="footer-widgets">
            <div class="footer-col">
                <?php if ( is_active_sidebar( 'footer-1' ) ) dynamic_sidebar( 'footer-1' ); ?>
            </div>
            <div class="footer-col">
                <?php if ( is_active_sidebar( 'footer-2' ) ) dynamic_sidebar( 'footer-2' ); ?>
            </div>
            <div class="footer-col">
                <?php if ( is_active_sidebar( 'footer-3' ) ) dynamic_sidebar( 'footer-3' ); ?>
            </div>
        </div>

        <div class="site-copyright">
            <?php 
                $copyright = !empty($options['copyright_text']) ? $options['copyright_text'] : '&copy; ' . date('Y') . ' ' . get_bloginfo('name');
                echo wp_kses_post( $copyright ); 
            ?>
        </div>
    </div>
</footer>

<div id="modal-login" class="mark-modal">
    <div class="modal-overlay"></div>
    <div class="modal-box">
        <span class="modal-close">&times;</span>
        <h3 class="modal-title">Đăng Nhập</h3>
        <div class="modal-body">
            <?php echo do_shortcode('[mark_login_form]'); ?>
            
            <div class="modal-footer-link">
                Chưa có tài khoản? <a href="#" class="js-switch-to-register">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>

<div id="modal-register" class="mark-modal">
    <div class="modal-overlay"></div>
    <div class="modal-box">
        <span class="modal-close">&times;</span>
        <h3 class="modal-title">Đăng Ký Thành Viên</h3>
        <div class="modal-body">
            <?php echo do_shortcode('[mark_register_form]'); ?>
            
            <div class="modal-footer-link">
                Đã có tài khoản? <a href="#" class="js-switch-to-login">Đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<?php 
if ( ! empty( $options['footer_code'] ) ) echo $options['footer_code'];
wp_footer(); 
?>
</body>
</html>