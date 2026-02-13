<?php
/**
 * Template Name: Terms & Conditions
 * 
 * Legal pages: terms, privacy, cookies, cancellation.
 * Assign this template to the "Terms" page in WP Admin.
 * Content loaded from page-content/terms.html
 */
get_header(); ?>

<?php echo mjsk_load_page_content('terms-styles.html'); ?>

<main id="main-content">
    <?php echo mjsk_load_page_content('terms.html'); ?>
</main>

<?php get_footer(); ?>
