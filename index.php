<?php
/**
 * Default fallback template.
 * WordPress requires index.php. Redirects to the homepage.
 */
get_header(); ?>

<main id="main-content">
    <section style="padding: 120px 20px 80px; text-align: center;">
        <div class="container">
            <h1>Welcome to Marbella JetSki</h1>
            <p>Please visit our <a href="<?php echo home_url('/'); ?>">homepage</a> to explore our services.</p>
        </div>
    </section>
</main>

<?php get_footer(); ?>
