<?php get_header(); ?>

<div class="container mark-wrapper">

    <div class="site-content">

        <main class="main-content">
            <?php 
            // Vòng lặp chính
            while ( have_posts() ) : the_post(); 
                
                $novel_id = get_the_ID(); 
            ?>

            <div class="novel-header">
                
                <div class="novel-thumb">
                    <div class="thumb-link">
                        <?php 
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('large'); 
                        } else {
                            echo '<img src="https://placehold.co/300x450?text=NO+COVER" alt="No Cover">';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="novel-info">
                    <h1 class="novel-title"><?php the_title(); ?></h1>
                    
                    <?php
                        // Đếm số chương
                        $count_query = new WP_Query([
                            'post_type'      => 'chapter',
                            'meta_key'       => '_parent_novel_id',
                            'meta_value'     => $novel_id,
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                            'no_found_rows'  => true,
                        ]);
                        $total_chapters = $count_query->post_count; 

                        // Lấy Nguồn
                        $source = get_post_meta($novel_id, '_novel_source', true);
                    ?>
                    
                    <div class="novel-meta">
                        <div class="novel-meta-row">
                            <strong><i class="dashicons dashicons-edit"></i> Tác giả:</strong> 
                            <?php echo get_the_term_list($novel_id, 'novel_author', '', ', ', ''); ?>
                        </div>
                        
                        <div class="novel-meta-row">
                            <strong><i class="dashicons dashicons-category"></i> Thể loại:</strong> 
                            <?php echo get_the_term_list($novel_id, 'novel_genre', '', ', ', ''); ?>
                        </div>
                        
                        <div class="novel-meta-row">
                            <strong><i class="dashicons dashicons-info"></i> Trạng thái:</strong> 
                            <?php echo get_the_term_list($novel_id, 'novel_status', '', ', ', ''); ?>
                        </div>
                        
                        <div class="novel-meta-row">
                            <strong><i class="dashicons dashicons-media-text"></i> Số chương:</strong> 
                            <span class="mark-badge"><?php echo $total_chapters; ?> chương</span>
                        </div>

                        <?php if (!empty($source)): ?>
                        <div class="novel-meta-row">
                            <strong><i class="dashicons dashicons-admin-links"></i> Nguồn:</strong> 
                            <span style="color: #555; font-style: italic;"><?php echo esc_html($source); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="novel-meta-row">
                            <strong><i class="dashicons dashicons-visibility"></i> Lượt xem:</strong> 
                            <?php 
                                $views = get_post_meta(get_the_ID(), 'post_views_count', true);
                                echo $views ? number_format_i18n($views) : '0'; 
                            ?>
                        </div>
                    </div>

                    <?php
                        // Tìm chương đầu/cuối
                        $first_chapter = get_posts([
                            'post_type'      => 'chapter',
                            'posts_per_page' => 1,
                            'meta_key'       => '_parent_novel_id',
                            'meta_value'     => get_the_ID(),
                            'orderby'        => 'date',
                            'order'          => 'ASC',
                        ]);
                        $first_chapter_url = $first_chapter ? get_permalink($first_chapter[0]->ID) : '#';

                        $last_chapter = get_posts([
                            'post_type'      => 'chapter',
                            'posts_per_page' => 1,
                            'meta_key'       => '_parent_novel_id',
                            'meta_value'     => get_the_ID(),
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                        ]);
                        $last_chapter_url = $last_chapter ? get_permalink($last_chapter[0]->ID) : '#';
                        
                        $like_count = get_post_meta(get_the_ID(), 'post_like_count', true) ?: 0;
                    ?>

                    <div class="novel-actions">
                        <?php if($first_chapter): ?>
                        <a href="<?php echo $first_chapter_url; ?>" class="btn-action btn-read-first">
                            <i class="dashicons dashicons-book"></i> Đọc Từ Đầu
                        </a>
                        <?php endif; ?>

                        <?php if($last_chapter): ?>
                        <a href="<?php echo $last_chapter_url; ?>" class="btn-action btn-read-last">
                            <i class="dashicons dashicons-clock"></i> Mới Nhất
                        </a>
                        <?php endif; ?>

                        <button class="btn-action btn-bookmark" 
                            data-id="<?php the_ID(); ?>" 
                            data-title="<?php the_title(); ?>" 
                            data-url="<?php the_permalink(); ?>"
                            data-thumb="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>">
                            <i class="dashicons dashicons-heart"></i> Lưu Truyện
                        </button>

                        <button class="btn-action btn-like" data-id="<?php the_ID(); ?>">
                            <i class="dashicons dashicons-thumbs-up"></i> 
                            <span class="like-text">Thích</span> 
                            <span class="like-count">(<?php echo $like_count; ?>)</span>
                        </button>

                        <button class="btn-action btn-report" data-id="<?php the_ID(); ?>">
                            <i class="dashicons dashicons-warning"></i> Báo Lỗi
                        </button>
                    </div>

                </div> 
            </div>

            <div class="novel-summary">
                <h3 class="section-title"><i class="dashicons dashicons-editor-alignleft"></i> Giới Thiệu Truyện</h3>
                <div class="summary-content">
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="chapter-list-box">
                <h3 class="section-title"><i class="dashicons dashicons-menu"></i> Danh Sách Chương</h3>
                <div class="chapter-grid">
                    <?php
                    $chapters = new WP_Query([
                        'post_type'      => 'chapter',
                        'meta_key'       => '_parent_novel_id',
                        'meta_value'     => $novel_id,
                        'posts_per_page' => -1,
                        'orderby'        => 'date',
                        'order'          => 'ASC'
                    ]);

                    if ($chapters->have_posts()) :
                        while ($chapters->have_posts()) : $chapters->the_post();
                    ?>
                        <div class="chapter-item">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                <i class="dashicons dashicons-media-document"></i> <?php the_title(); ?>
                            </a>
                        </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>Đang cập nhật chương mới...</p>';
                    endif;
                    ?>
                </div>
            </div>

            <div class="report-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
                <div style="background:#fff; width:90%; max-width:400px; padding:25px; border-radius:8px; position:relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                    <span class="close-modal" style="position:absolute; right:15px; top:10px; cursor:pointer; font-size:24px; color: #999;">&times;</span>
                    <h3 style="margin-top:0; color: var(--primary);">Báo lỗi truyện</h3>
                    <p style="font-size:14px; color:#666;">Vui lòng mô tả lỗi bạn gặp phải:</p>
                    <textarea id="report-content" rows="4" style="width:100%; margin:10px 0; padding:10px; border:1px solid #ddd; border-radius:4px;" placeholder="Ví dụ: Ảnh bìa lỗi, sai thông tin..."></textarea>
                    <button id="report-submit" style="width:100%; background:var(--primary); color:#fff; padding:10px; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">Gửi Báo Cáo</button>
                </div>
            </div>

            <?php endwhile; ?>
        </main> <?php get_sidebar(); ?>
        
    </div> </div>

<?php get_footer(); ?>