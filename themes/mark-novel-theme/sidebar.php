<aside id="secondary" class="widget-area">
    <?php
    if ( is_active_sidebar( 'main-sidebar' ) ) {
        dynamic_sidebar( 'main-sidebar' );
    } else {
        // Nội dung mặc định nếu chưa kéo widget
        echo '<div class="widget"><h3 class="widget-title">Thông báo</h3><p>Chưa có widget nào. Hãy vào Admin -> Giao diện -> Widget để thêm.</p></div>';
    }
    ?>
</aside>