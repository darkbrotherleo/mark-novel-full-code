<?php get_header(); ?>

<div class="container" style="margin-top: 30px;">

    <section class="latest-updates">
        <h2 style="border-left: 5px solid #0073aa; padding-left: 10px; margin-bottom: 20px;">
            Mới Cập Nhật
        </h2>

        <div class="novel-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px;">
            <?php
            // Query lấy 8 truyện mới nhất
            $args = [
                'post_type' => 'novel',
                'posts_per_page' => 8,
                'orderby' => 'date',
                'order' => 'DESC'
            ];
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
            ?>
                <div class="novel-card" style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <a href="<?php the_permalink(); ?>" class="thumb-link" style="display: block; height: 240px; overflow: hidden;">
                        <?php 
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('medium', ['style' => 'width: 100%; height: 100%; object-fit: cover; transition: 0.3s;']);
                        } else {
                            echo '<img src="https://placehold.co/200x300" style="width: 100%; height: 100%; object-fit: cover;">';
                        }
                        ?>
                    </a>
                    <div class="novel-body" style="padding: 10px;">
                        <h3 style="font-size: 16px; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div style="font-size: 12px; color: #888;">
                            <?php 
                            // Lấy tên tác giả (Taxonomy)
                            $authors = get_the_terms(get_the_ID(), 'novel_author');
                            echo $authors ? $authors[0]->name : 'Đang cập nhật';
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>Chưa có truyện nào.</p>';
            endif;
            ?>
        </div>
    </section>

</div>

<?php get_footer(); ?>