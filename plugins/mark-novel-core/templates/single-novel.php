<?php get_header(); ?>

<?php 
// 1. DỮ LIỆU TRUYỆN
$novel_id = get_the_ID(); 
$thumb_url = get_the_post_thumbnail_url($novel_id, 'full') ?: get_template_directory_uri() . '/assets/images/no-thumb.jpg';
$author_terms = get_the_terms($novel_id, 'tac-gia');
$author_name = ($author_terms && !is_wp_error($author_terms)) ? $author_terms[0]->name : 'Đang cập nhật';
$genres = get_the_category($novel_id);
if(empty($genres)) $genres = get_the_terms($novel_id, 'the-loai');

// 2. LẤY LIST CHƯƠNG (Query chuẩn)
$chapter_args = [
    'post_type'      => 'chapter',
    'post_parent'    => $novel_id,  // Lấy các chương có cha là truyện này
    'posts_per_page' => -1,
    'orderby'        => 'date',     // Sắp xếp ngày đăng
    'order'          => 'ASC',      // Cũ nhất (Chương 1) lên đầu
    'post_status'    => 'publish'
];
$chapter_query = new WP_Query($chapter_args);
$total_chapters = $chapter_query->found_posts;

// Xử lý link
$first_chap_link = '#';
$latest_chap_link = '#';
$latest_time = 'Chưa có';

if ($chapter_query->have_posts()) {
    $first_chap_link = get_permalink($chapter_query->posts[0]->ID);
    $last_post = $chapter_query->posts[count($chapter_query->posts) - 1];
    $latest_chap_link = get_permalink($last_post->ID);
    $latest_time = human_time_diff(get_the_modified_time('U', $last_post->ID), current_time('timestamp')) . ' trước';
}
?>

<div class="novel-page-wrapper">
    <div class="novel-hero-section" style="background-image: url('<?php echo esc_url($thumb_url); ?>');">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-thumb"><img src="<?php echo esc_url($thumb_url); ?>" class="book-cover-3d"></div>
                <div class="hero-info">
                    <h1 class="novel-title-main"><?php the_title(); ?></h1>
                    <ul class="hero-meta">
                        <li><i class="dashicons dashicons-edit"></i> Tác giả: <strong><?php echo esc_html($author_name); ?></strong></li>
                        <li><i class="dashicons dashicons-category"></i> Thể loại: 
                            <?php if($genres && !is_wp_error($genres)) { foreach($genres as $g) echo '<span style="margin-right:5px">'.$g->name.'</span>'; } ?>
                        </li>
                        <li><i class="dashicons dashicons-clock"></i> Cập nhật: <?php echo $latest_time; ?></li>
                    </ul>
                    <div class="hero-stats">
                        <div class="stat-item"><strong><?php echo number_format_i18n((int)get_post_meta($novel_id, 'post_views_count', true)); ?></strong><span>Lượt xem</span></div>
                        <div class="stat-item"><strong><?php echo $total_chapters; ?></strong><span>Chương</span></div>
                        <div class="stat-item"><strong><?php echo get_post_meta($novel_id, 'total_likes', true) ?: 0; ?></strong><span>Thích</span></div>
                    </div>
                    <div class="hero-actions">
                        <?php if($total_chapters > 0): ?>
                            <a href="<?php echo esc_url($first_chap_link); ?>" class="btn-hero-primary">Đọc Từ Đầu</a>
                            <a href="<?php echo esc_url($latest_chap_link); ?>" class="btn-hero-secondary">Mới Nhất</a>
                        <?php else: ?>
                            <button class="btn-hero-secondary" disabled>Chưa có chương</button>
                        <?php endif; ?>
                        <button class="btn-hero-secondary btn-bookmark" data-id="<?php echo $novel_id; ?>"><i class="dashicons dashicons-heart"></i> Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mark-wrapper">
        <div class="site-content-grid">
            <main id="main" class="site-main-clean">
                <?php while ( have_posts() ) : the_post(); ?>
                
                <div class="novel-section">
                    <h3 class="section-h3">Giới Thiệu</h3>
                    <div class="entry-content summary-content"><?php the_content(); ?></div>
                </div>
                <div class="novel-divider"></div>

                <div id="chapter-list" class="novel-section">
                    <div class="section-header-flex">
                        <h3 class="section-h3">Danh Sách Chương (<?php echo $total_chapters; ?>)</h3>
                        <span class="chapter-sort-btn"><i class="dashicons dashicons-sort"></i> Cũ nhất trước</span>
                    </div>

                    <div class="clean-chapter-list">
                        <?php if ($chapter_query->have_posts()) : ?>
                            <?php while ($chapter_query->have_posts()) : $chapter_query->the_post(); ?>
                                <a href="<?php the_permalink(); ?>" class="clean-chapter-item">
                                    <span class="chap-num"><?php the_title(); ?></span>
                                    <span class="chap-date"><?php echo get_the_date('d/m/Y'); ?></span>
                                </a>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php else : ?>
                            <p style="padding:20px; text-align:center; color:#777; width:100%; grid-column:1/-1;">Chưa có chương nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php endwhile; ?>
            </main>
            <aside class="sidebar-clean"><?php get_sidebar(); ?></aside>
        </div>
    </div>
</div>
<?php get_footer(); ?>