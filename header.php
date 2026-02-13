<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Enhanced SEO -->
    <meta name="author" content="Marbella JetSki - Daniel Stiers">
    <link rel="icon" type="image/png" href="<?php echo mjsk_asset('media/photos/logo-circular.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo mjsk_asset('media/photos/logo-circular.png'); ?>">
    <meta name="theme-color" content="#0ea5e9">
    
    <?php wp_head(); ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 50, delay: 0 });
            }
        });
    </script>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <a href="#main-content" class="skip-link">Skip to content</a>

    <!-- Navigation -->
    <nav class="navbar<?php echo !is_front_page() ? ' scrolled' : ''; ?>" id="navbar">
        <div class="nav-container">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-logo">
                <img src="<?php echo mjsk_asset('media/photos/logo-circular.png'); ?>" alt="Marbella JetSki Logo" class="logo-image" style="height: 75px;">
                <span class="logo-text">MARBELLA<span class="logo-highlight">JETSKI</span></span>
            </a>
            
            <div class="nav-menu" id="navMenu">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'walker'         => new MJSK_Nav_Walker(),
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'fallback_cb'    => function() {
                        // Fallback if no menu assigned yet
                        echo '<a href="' . home_url('/') . '" class="nav-link active">Home</a>';
                        echo '<a href="' . home_url('/booking/') . '" class="nav-link">Book Now</a>';
                        echo '<a href="' . home_url('/#contact') . '" class="nav-link">Contact</a>';
                    },
                ]);
                ?>
                <!-- Mobile-only footer (hidden on desktop by CSS) -->
                <div class="mobile-menu-footer">
                    <?php if (function_exists('pll_the_languages')) : ?>
                        <!-- Polylang language switcher -->
                        <div class="nav-lang-dropdown">
                            <button class="nav-lang-btn" aria-label="Language">
                                <?php pll_current_language('flag'); ?>
                                <span><?php echo strtoupper(pll_current_language('slug')); ?></span>
                                <i class="fas fa-chevron-down" style="font-size:10px;margin-left:2px"></i>
                            </button>
                            <div class="nav-lang-menu">
                                <?php pll_the_languages(['show_flags' => 1, 'show_names' => 1]); ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Static language switcher (until Polylang is installed) -->
                        <div class="nav-lang-dropdown">
                            <button class="nav-lang-btn" aria-label="Language">
                                <img src="https://flagcdn.com/w40/gb.png" alt="English" width="24" height="16">
                                <span>EN</span>
                                <i class="fas fa-chevron-down" style="font-size:10px;margin-left:2px"></i>
                            </button>
                            <div class="nav-lang-menu">
                                <a href="<?php echo home_url('/'); ?>" class="active"><img src="https://flagcdn.com/w40/gb.png" alt="English" width="20" height="14"> English</a>
                                <a href="<?php echo home_url('/es/'); ?>"><img src="https://flagcdn.com/w40/es.png" alt="Español" width="20" height="14"> Español</a>
                                <a href="<?php echo home_url('/fr/'); ?>"><img src="https://flagcdn.com/w40/fr.png" alt="Français" width="20" height="14"> Français</a>
                                <a href="<?php echo home_url('/nl/'); ?>"><img src="https://flagcdn.com/w40/nl.png" alt="Nederlands" width="20" height="14"> Nederlands</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo home_url('/booking/'); ?>" class="nav-cta"><span>Book Now</span><i class="fas fa-calendar-check"></i></a>
                </div>
            </div>
            
            <div class="nav-actions">
                <?php if (function_exists('pll_the_languages')) : ?>
                    <div class="nav-lang-dropdown">
                        <button class="nav-lang-btn" aria-label="Language">
                            <?php pll_current_language('flag'); ?>
                            <span><?php echo strtoupper(pll_current_language('slug')); ?></span>
                            <i class="fas fa-chevron-down" style="font-size:10px;margin-left:2px"></i>
                        </button>
                        <div class="nav-lang-menu">
                            <?php pll_the_languages(['show_flags' => 1, 'show_names' => 1]); ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="nav-lang-dropdown">
                        <button class="nav-lang-btn" aria-label="Language">
                            <img src="https://flagcdn.com/w40/gb.png" alt="English" width="24" height="16">
                            <span>EN</span>
                            <i class="fas fa-chevron-down" style="font-size:10px;margin-left:2px"></i>
                        </button>
                        <div class="nav-lang-menu">
                            <a href="<?php echo home_url('/'); ?>" class="active"><img src="https://flagcdn.com/w40/gb.png" alt="English" width="20" height="14"> English</a>
                            <a href="<?php echo home_url('/es/'); ?>"><img src="https://flagcdn.com/w40/es.png" alt="Español" width="20" height="14"> Español</a>
                            <a href="<?php echo home_url('/fr/'); ?>"><img src="https://flagcdn.com/w40/fr.png" alt="Français" width="20" height="14"> Français</a>
                            <a href="<?php echo home_url('/nl/'); ?>"><img src="https://flagcdn.com/w40/nl.png" alt="Nederlands" width="20" height="14"> Nederlands</a>
                        </div>
                    </div>
                <?php endif; ?>
                <a href="<?php echo home_url('/booking/'); ?>" class="nav-cta">
                    <span>Book Now</span>
                    <i class="fas fa-calendar-check"></i>
                </a>
            </div>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <?php if (is_front_page() && mjsk_get('mjsk_promo_enabled')) : ?>
    <!-- Promo Banner (homepage only) -->
    <div class="hero-promo-overlay">
        <span class="hero-promo-icon">☀️</span>
        <div class="hero-promo-text">
            <strong><?php echo esc_html(mjsk_get('mjsk_promo_title')); ?></strong>
            <span><?php echo esc_html(mjsk_get('mjsk_promo_text')); ?></span>
        </div>
        <a href="<?php echo home_url('/booking/'); ?>" class="hero-promo-btn">
            <i class="fas fa-calendar-check"></i>
            Book Now
        </a>
    </div>
    <?php endif; ?>
