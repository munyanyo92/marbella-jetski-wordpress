<?php
/**
 * 404 Page Not Found template.
 */
get_header(); ?>

<main id="main-content">
    <section class="hero" style="min-height: 60vh; display: flex; align-items: center;">
        <div class="container" style="text-align: center; padding: 120px 20px 80px;">
            <h1 style="font-size: 72px; color: var(--primary); margin-bottom: 20px;">404</h1>
            <h2 style="margin-bottom: 20px;">Page Not Found</h2>
            <p style="color: var(--gray-600); margin-bottom: 30px;">The page you're looking for doesn't exist or has been moved.</p>
            <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to Homepage
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
