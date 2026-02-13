<?php
/**
 * Template Name: Lessons
 * 
 * Racing lessons page with Daniel's competition section.
 * Assign this template to the "Lessons" page in WP Admin.
 * Content loaded from page-content/lessons.html
 */
get_header(); ?>

<?php
// Load page-specific inline styles
echo mjsk_load_page_content('lessons-styles.html');
?>

<main id="main-content">
    <?php echo mjsk_load_page_content('lessons.html'); ?>
</main>

<?php get_footer(); ?>
