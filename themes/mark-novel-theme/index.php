<?php get_header(); ?>

<div class="container mark-wrapper">
    <div class="site-content">
        
        <main class="main-content">
            <?php if ( have_posts() ) : ?>
                <div class="novel-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <div class="novel-card">
                            <div class="novel-body">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="novel-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="pagination">
                    <?php the_posts_pagination(); ?>
                </div>
            <?php else : ?>
                <p>Không tìm thấy nội dung nào.</p>
            <?php endif; ?>
        </main>

        <?php get_sidebar(); ?>

    </div>
</div>

<?php get_footer(); ?>