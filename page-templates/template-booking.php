<?php
/**
 * Template Name: Booking
 * 
 * Booking page template with the full booking form.
 * Assign this template to the "Booking" page in WP Admin.
 * Content loaded from page-content/booking.html
 */
get_header(); ?>

<?php
// Load page-specific inline styles
echo mjsk_load_page_content('booking-styles.html');
?>

<main id="main-content">
    <?php echo mjsk_load_page_content('booking.html'); ?>
</main>

<?php get_footer(); ?>
