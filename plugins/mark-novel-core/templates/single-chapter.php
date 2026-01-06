<?php get_header(); ?>

<?php 
// 1. Lấy thông tin truyện cha để lưu lịch sử
$parent_id = get_post_meta(get_the_ID(), '_parent_novel_id', true);
$parent_title = $parent_id ? get_the_title($parent_id) : '';
$parent_thumb = $parent_id ? get_the_post_thumbnail_url($parent_id, 'thumbnail') : '';
?>

<div id="chapter-tracking-data" 
    data-novel-id="<?php echo esc_attr($parent_id); ?>" 
    data-novel-title="<?php echo esc_attr($parent_title); ?>"
    data-novel-thumb="<?php echo esc_attr($parent_thumb); ?>"
    data-chapter-title="<?php the_title(); ?>"
    data-chapter-url="<?php the_permalink(); ?>"
    style="display:none;">
</div>

<div class="container mark-wrapper">

    <div class="site-content">
        
        <main class="main-content">
            <?php while ( have_posts() ) : the_post(); 
                $current_id = get_the_ID();
                $parent_id = get_post_meta($current_id, '_parent_novel_id', true);
                $novel_link = $parent_id ? get_permalink($parent_id) : home_url();
                $novel_title = $parent_id ? get_the_title($parent_id) : 'Trang chủ';
            ?>

            <div style="margin-bottom: 20px; color: #666; font-size: 0.9rem;">
                <a href="<?php echo $novel_link; ?>" style="text-decoration:none; color: #0073aa;">
                    &larr; <?php echo $novel_title; ?>
                </a>
            </div>

            <h1 style="text-align: center; margin-bottom: 40px; color: #333; font-family: 'Roboto', sans-serif;">
                <?php the_title(); ?>
            </h1>

            <div class="chapter-reading-area">
                <?php the_content(); ?>
            </div>

            <?php
                $prev_post = get_posts([
                    'post_type' => 'chapter',
                    'meta_key' => '_parent_novel_id', 'meta_value' => $parent_id,
                    'posts_per_page' => 1,
                    'date_query' => ['before' => get_the_date('Y-m-d H:i:s')],
                    'orderby' => 'date', 'order' => 'DESC'
                ]);
                
                $next_post = get_posts([
                    'post_type' => 'chapter',
                    'meta_key' => '_parent_novel_id', 'meta_value' => $parent_id,
                    'posts_per_page' => 1,
                    'date_query' => ['after' => get_the_date('Y-m-d H:i:s')],
                    'orderby' => 'date', 'order' => 'ASC'
                ]);
                
                $prev_link = !empty($prev_post) ? get_permalink($prev_post[0]->ID) : '#';
                $next_link = !empty($next_post) ? get_permalink($next_post[0]->ID) : '#';
                $prev_cls = empty($prev_post) ? 'disabled' : '';
                $next_cls = empty($next_post) ? 'disabled' : '';
            ?>

            <div class="chapter-nav">
                <a href="<?php echo $prev_link; ?>" class="nav-btn <?php echo $prev_cls; ?>">« Trước</a>
                <a href="<?php echo $novel_link; ?>" class="nav-btn">Mục Lục</a>
                <a href="<?php echo $next_link; ?>" class="nav-btn <?php echo $next_cls; ?>">Sau »</a>
            </div>

            <?php endwhile; ?>
        </main> <?php get_sidebar(); ?>

    </div> </div>

<?php get_footer(); ?>