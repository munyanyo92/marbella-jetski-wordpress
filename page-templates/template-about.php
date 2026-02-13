<?php
/**
 * Template Name: About Us
 * 
 * About Us page with Daniel's story, team, and achievements.
 * Assign this template to the "About Us" page in WP Admin.
 * Content loaded from page-content/about-us.html
 */
get_header(); ?>

<?php
echo mjsk_load_page_content('about-us-styles.html');
?>

<main id="main-content">
    <?php echo mjsk_load_page_content('about-us.html'); ?>
</main>

<?php get_footer(); ?>
