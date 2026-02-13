<?php
/**
 * Marbella JetSki - Theme Functions
 *
 * Core WordPress integration: menus, scripts, customizer, helpers.
 * All page content is loaded from static HTML files in /page-content/
 * and asset paths are auto-converted to WordPress theme URIs.
 *
 * @package MarbellaJetSki
 */

// ============================================================
//  THEME SETUP
// ============================================================
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);

    // Navigation menus editable in WP Admin → Appearance → Menus
    register_nav_menus([
        'primary'      => __('Primary Navigation (Header)', 'marbellajetski'),
        'footer-quick' => __('Footer - Quick Links', 'marbellajetski'),
        'footer-info'  => __('Footer - Information Links', 'marbellajetski'),
    ]);
});


// ============================================================
//  ENQUEUE STYLES & SCRIPTS
// ============================================================
add_action('wp_enqueue_scripts', function () {
    $v = wp_get_theme()->get('Version');
    $uri = get_template_directory_uri();

    // ── CSS ──
    wp_enqueue_style('google-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap',
        [], null
    );
    wp_enqueue_style('font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        [], '6.5.1'
    );
    wp_enqueue_style('aos-css',
        'https://unpkg.com/aos@2.3.1/dist/aos.css',
        [], '2.3.1'
    );
    wp_enqueue_style('swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [], '11'
    );
    wp_enqueue_style('mjsk-main', $uri . '/assets/css/main.css', [], $v);

    // ── JS ──
    wp_enqueue_script('aos-js',
        'https://unpkg.com/aos@2.3.1/dist/aos.js',
        [], '2.3.1', true
    );
    wp_enqueue_script('swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [], '11', true
    );
    wp_enqueue_script('mjsk-main', $uri . '/assets/js/script.js', ['aos-js', 'swiper-js'], $v, true);

    // Pass theme data to JS
    wp_localize_script('mjsk-main', 'mjskData', [
        'themeUrl' => $uri,
        'homeUrl'  => home_url('/'),
        'ajaxUrl'  => admin_url('admin-ajax.php'),
    ]);
});


// ============================================================
//  CUSTOM NAV WALKER — outputs <a class="nav-link"> directly
//  (matches the static site markup, no <ul><li> wrappers)
// ============================================================
class MJSK_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
        $classes = '';
        if (in_array('current-menu-item', $item->classes) || in_array('current-page-ancestor', $item->classes)) {
            $classes = ' active';
        }
        $output .= '<a href="' . esc_url($item->url) . '" class="nav-link' . $classes . '">';
        $output .= esc_html($item->title);
    }

    public function end_el(&$output, $item, $depth = 0, $args = []) {
        $output .= '</a>' . "\n";
    }

    // No <ul> wrappers for flat nav
    public function start_lvl(&$output, $depth = 0, $args = []) {}
    public function end_lvl(&$output, $depth = 0, $args = []) {}
}


// ============================================================
//  HELPER — Load static page content with path rewriting
// ============================================================
/**
 * Loads a static HTML file from /page-content/ and rewrites all
 * local asset paths and page links to WordPress-compatible URLs.
 *
 * @param string $filename  File in the theme's page-content/ directory
 * @return string           Processed HTML content
 */
function mjsk_load_page_content($filename) {
    $file = get_template_directory() . '/page-content/' . $filename;
    if (!file_exists($file)) {
        return '<!-- Page content not found: ' . esc_html($filename) . ' -->';
    }

    $content  = file_get_contents($file);
    $theme_uri = get_template_directory_uri();
    $home      = home_url('/');

    // Asset paths  (src="assets/...", poster="assets/...", href="assets/...")
    $content = preg_replace(
        '#((?:src|poster|href)\s*=\s*["\'])(?:\.\./)*assets/#i',
        '$1' . $theme_uri . '/assets/',
        $content
    );

    // Internal page links  (.html → WP slugs)
    $page_map = [
        'booking.html'        => 'booking/',
        'index.html'          => '',
        'lessons.html'        => 'lessons/',
        'about-us.html'       => 'about-us/',
        'terms.html'          => 'terms/',
        'weather-policy.html' => 'weather-policy/',
        'es/index.html'       => 'es/',
        'es/booking.html'     => 'es/booking/',
        'fr/index.html'       => 'fr/',
        'nl/index.html'       => 'nl/',
    ];
    foreach ($page_map as $static => $wp_slug) {
        $content = str_replace(
            'href="' . $static,
            'href="' . $home . $wp_slug,
            $content
        );
    }

    return $content;
}


// ============================================================
//  HELPER — Quick asset URL
// ============================================================
function mjsk_asset($path) {
    return get_template_directory_uri() . '/assets/' . ltrim($path, '/');
}


// ============================================================
//  CUSTOMIZER — Editable settings in Appearance → Customize
//  (phone, email, WhatsApp, social links, promo banner)
// ============================================================
add_action('customize_register', function ($wp_customize) {

    // ── Contact Details ──
    $wp_customize->add_section('mjsk_contact', [
        'title'    => __('Contact Details', 'marbellajetski'),
        'priority' => 30,
    ]);

    $fields = [
        'mjsk_phone'    => ['+34 655 442 232', 'Phone Number'],
        'mjsk_email'    => ['jetskimarbella@gmail.com', 'Email Address'],
        'mjsk_whatsapp' => ['34655442232', 'WhatsApp Number (no +, no spaces)'],
        'mjsk_address'  => ['Playa de las Dunas, 29604 Marbella, Málaga', 'Address'],
        'mjsk_hours'    => ['Daily: 11am - 8pm', 'Opening Hours'],
    ];
    foreach ($fields as $id => [$default, $label]) {
        $wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control($id, ['label' => $label, 'section' => 'mjsk_contact', 'type' => 'text']);
    }

    // ── Social Media ──
    $wp_customize->add_section('mjsk_social', [
        'title'    => __('Social Media Links', 'marbellajetski'),
        'priority' => 35,
    ]);

    $socials = [
        'mjsk_facebook'    => ['https://www.facebook.com/jetskimarbella/', 'Facebook URL'],
        'mjsk_instagram'   => ['https://www.instagram.com/jetskimarbella/', 'Instagram URL'],
        'mjsk_tiktok'      => ['https://www.tiktok.com/@jetskimarbella', 'TikTok URL'],
        'mjsk_youtube'     => ['https://www.youtube.com/@marbellajetski', 'YouTube URL'],
        'mjsk_tripadvisor' => ['https://www.tripadvisor.es/Attraction_Review-g187439-d6949698-Reviews-Marbella_Jet_Ski-Marbella_Costa_del_Sol_Province_of_Malaga_Andalucia.html', 'TripAdvisor URL'],
    ];
    foreach ($socials as $id => [$default, $label]) {
        $wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control($id, ['label' => $label, 'section' => 'mjsk_social', 'type' => 'url']);
    }

    // ── Promo Banner ──
    $wp_customize->add_section('mjsk_promo', [
        'title'    => __('Promo Banner', 'marbellajetski'),
        'priority' => 40,
    ]);

    $wp_customize->add_setting('mjsk_promo_enabled', ['default' => true, 'sanitize_callback' => 'wp_validate_boolean']);
    $wp_customize->add_control('mjsk_promo_enabled', ['label' => 'Show Promo Banner', 'section' => 'mjsk_promo', 'type' => 'checkbox']);

    $wp_customize->add_setting('mjsk_promo_title', ['default' => 'Book Before Summer & Save 10%!', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('mjsk_promo_title', ['label' => 'Promo Title', 'section' => 'mjsk_promo', 'type' => 'text']);

    $wp_customize->add_setting('mjsk_promo_text', ['default' => 'Early bird discount on all jet ski & yacht bookings for June–September 2026', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('mjsk_promo_text', ['label' => 'Promo Subtitle', 'section' => 'mjsk_promo', 'type' => 'text']);
});

/**
 * Get a customizer value with its default.
 */
function mjsk_get($key) {
    $defaults = [
        'mjsk_phone'         => '+34 655 442 232',
        'mjsk_email'         => 'jetskimarbella@gmail.com',
        'mjsk_whatsapp'      => '34655442232',
        'mjsk_address'       => 'Playa de las Dunas, 29604 Marbella, Málaga',
        'mjsk_hours'         => 'Daily: 11am - 8pm',
        'mjsk_facebook'      => 'https://www.facebook.com/jetskimarbella/',
        'mjsk_instagram'     => 'https://www.instagram.com/jetskimarbella/',
        'mjsk_tiktok'        => 'https://www.tiktok.com/@jetskimarbella',
        'mjsk_youtube'       => 'https://www.youtube.com/@marbellajetski',
        'mjsk_tripadvisor'   => 'https://www.tripadvisor.es/Attraction_Review-g187439-d6949698-Reviews-Marbella_Jet_Ski-Marbella_Costa_del_Sol_Province_of_Malaga_Andalucia.html',
        'mjsk_promo_enabled' => true,
        'mjsk_promo_title'   => 'Book Before Summer & Save 10%!',
        'mjsk_promo_text'    => 'Early bird discount on all jet ski & yacht bookings for June–September 2026',
    ];
    return get_theme_mod($key, $defaults[$key] ?? '');
}


// ============================================================
//  STRUCTURED DATA (JSON-LD)
// ============================================================
add_action('wp_head', function () {
    if (is_front_page()) : ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Marbella JetSki",
        "description": "Premier jet ski hire, luxury boat rentals & water sports in Marbella",
        "url": "<?php echo esc_url(home_url('/')); ?>",
        "telephone": "<?php echo esc_attr(mjsk_get('mjsk_phone')); ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Arroyo de la playa de las dunas, Urbanización Pinomar",
            "addressLocality": "Marbella",
            "postalCode": "29604",
            "addressRegion": "Málaga",
            "addressCountry": "ES"
        },
        "geo": { "@type": "GeoCoordinates", "latitude": "36.4958676", "longitude": "-4.8009563" },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
            "opens": "11:00", "closes": "20:00"
        },
        "priceRange": "€€",
        "aggregateRating": { "@type": "AggregateRating", "ratingValue": "4.9", "reviewCount": "500" },
        "founder": { "@type": "Person", "name": "Daniel Stiers", "jobTitle": "Founder & Pro Racing Champion" },
        "foundingDate": "1998"
    }
    </script>
    <?php endif;
});


// ============================================================
//  REMOVE EMOJI SCRIPTS (performance)
// ============================================================
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
});
