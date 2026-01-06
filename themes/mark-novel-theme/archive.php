<?php get_header(); ?>

<div class="container">
    
    <header class="archive-header" style="margin: 30px 0; border-bottom: 1px solid var(--border); padding-bottom: 20px;">
        <h1 class="page-title" style="font-size: 28px; color: var(--primary); text-transform: uppercase;">
            <?php the_archive_title(); ?>
        </h1>
        <?php if ( get_the_archive_description() ) : ?>
            <div class="archive-description" style="color: var(--text-light); margin-top: 10px;">
                <?php the_archive_description(); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="site-content">
        
        <main class="main-content">
            
            <?php if ( have_posts() ) : ?>
                
                <div class="novel-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        
                        <div class="novel-card">
                            <a href="<?php the_permalink(); ?>" class="thumb-link">
                                <?php 
                                if (has_post_thumbnail()) {
                                    // Dùng size large để ảnh nét, CSS sẽ lo việc crop
                                    the_post_thumbnail('large'); 
                                } else {
                                    echo '<img src="https://placehold.co/300x450?text=NO+COVER" alt="No Cover">';
                                }
                                ?>
                            </a>
                            
                            <div class="novel-body">
                                <h3>
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <div class="novel-meta">
                                    <span class="meta-item">
                                        <i class="dashicons dashicons-edit"></i>
                                        <?php 
                                        $authors = get_the_terms(get_the_ID(), 'novel_author');
                                        if ($authors && !is_wp_error($authors)) {
                                            echo esc_html($authors[0]->name);
                                        } else {
                                            echo 'Đang cập nhật';
                                        }
                                        ?>
                                    </span>
                                    
                                    <?php 
                                    $status = get_the_terms(get_the_ID(), 'novel_status');
                                    if ($status && !is_wp_error($status)) {
                                        echo ' &bull; <span style="color: var(--accent);">' . esc_html($status[0]->name) . '</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size'  => 2,
                        'prev_text' => '«',
                        'next_text' => '»',
                    ]);
                    ?>
                </div>

            <?php else : ?>
                <div class="no-results">
                    <p>Chưa có truyện nào trong mục này.</p>
                </div>
            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>

    </div> </div> <?php get_footer(); ?>