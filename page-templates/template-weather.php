<?php
/**
 * Template Name: Weather Policy
 * 
 * Weather and cancellation policy page.
 * Assign this template to the "Weather Policy" page in WP Admin.
 * Content loaded from page-content/weather-policy.html
 */
get_header(); ?>

<?php echo mjsk_load_page_content('weather-policy-styles.html'); ?>

<main id="main-content">
    <?php echo mjsk_load_page_content('weather-policy.html'); ?>
</main>

<?php get_footer(); ?>
