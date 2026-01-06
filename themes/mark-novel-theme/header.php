<?php $options = get_option( 'mark_theme_options' ); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('mark_theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('theme-dark');
            }
        })();
    </script>
    
    <?php wp_head(); ?>
    <?php if ( ! empty( $options['favicon_url'] ) ) : ?>
        <link rel="shortcut icon" href="<?php echo esc_url( $options['favicon_url'] ); ?>" />
    <?php endif; ?>
    <?php if ( ! empty( $options['header_code'] ) ) echo $options['header_code']; ?>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) wp_body_open(); ?>
<?php if ( ! empty( $options['body_code'] ) ) echo $options['body_code']; ?>

<header id="masthead" class="site-header">
    <div class="container"> <div class="header-inner">

<div class="site-branding">
    <?php 
    // 1. Ưu tiên Logo chuẩn WordPress
    if ( has_custom_logo() ) {
        the_custom_logo();
    } 
    // 2. Nếu không có Logo chuẩn, check xem Theme Options có logo không (Backup cho theme cũ)
    elseif ( !empty($options['logo_url']) ) {
        echo '<a href="' . home_url('/') . '" class="custom-logo-link"><img src="' . esc_url($options['logo_url']) . '" class="custom-logo" alt="Logo"></a>';
    }
    // 3. Nếu không có cả 2 thì hiện Tên Web
    else {
        echo '<h1 class="site-title"><a href="' . home_url('/') . '">' . get_bloginfo('name') . '</a></h1>';
    }
    ?>
</div>

            <nav class="main-navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="bar"></span><span class="bar"></span><span class="bar"></span>
                </button>
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'menu_class'     => 'nav-menu',
                ]);
                ?>
            </nav>

            <div class="header-tools">
                
                <div class="header-search-icon">
                    <span id="search-toggle"><i class="dashicons dashicons-search"></i></span>
                    <form role="search" method="get" class="header-search-form" action="<?php echo home_url( '/' ); ?>">
                        <input type="search" class="search-field" placeholder="Tìm..." value="<?php echo get_search_query(); ?>" name="s" />
                    </form>
                </div>

                <div class="theme-switcher">
                    <button id="theme-toggle" title="Giao diện Sáng/Tối">
                        <span class="icon-sun">☀️</span>
                        <span class="icon-moon">🌙</span>
                    </button>
                </div>

                <div class="header-user">
                    <?php if ( is_user_logged_in() ) : ?>
                        <?php $current_user = wp_get_current_user(); ?>
                        
                        <div class="user-logged">
                            <a href="<?php echo home_url('/tai-khoan/'); ?>" class="user-trigger">
                                <?php echo get_avatar( $current_user->ID, 32 ); ?>
                                <span class="user-name"><?php echo esc_html( $current_user->display_name ); ?></span>
                            </a>
                            
                            <ul class="user-dropdown">
                                <li><a href="<?php echo home_url('/tai-khoan/?tab=info'); ?>">Hồ sơ</a></li>
                                <li><a href="<?php echo home_url('/tai-khoan/?tab=bookmarks'); ?>">Tủ truyện</a></li>
                                <li><a href="<?php echo home_url('/tai-khoan/?tab=history'); ?>">Lịch sử</a></li>
                                <li class="sep"></li>
                                <li><a href="<?php echo wp_logout_url(home_url()); ?>" class="logout">Đăng xuất</a></li>
                            </ul>
                        </div>

                    <?php else : ?>
                        
                        <div class="user-guest">
                            <a href="#" class="btn-login js-open-login">Đăng nhập</a>
                            <a href="#" class="btn-register js-open-register">Đăng ký</a>
                        </div>

                    <?php endif; ?>
                </div>

            </div> </div> </div> </header>