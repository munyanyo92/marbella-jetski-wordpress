<?php
/**
 * Default page template.
 * Used for pages without a specific template assigned.
 * For editable content via WP editor.
 */
get_header(); ?>

<main id="main-content">
    <section style="padding: 120px 20px 80px;">
        <div class="container">
            <?php while ( have_posts() ) : the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <div class="wp-content-area">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
