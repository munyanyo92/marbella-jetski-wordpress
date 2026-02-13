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
    add_theme_support('custom-logo', ['width' => 200, 'height' => 200, 'flex-height' => true]);
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');

    // Navigation menus editable in WP Admin → Appearance → Menus
    register_nav_menus([
        'primary'      => __('Primary Navigation (Header)', 'marbellajetski'),
        'footer-quick' => __('Footer - Quick Links', 'marbellajetski'),
        'footer-info'  => __('Footer - Information Links', 'marbellajetski'),
    ]);
});


// ============================================================
//  WIDGET AREAS
// ============================================================
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Footer Widget Area', 'marbellajetski'),
        'id'            => 'footer-widgets',
        'description'   => __('Add widgets to the footer area.', 'marbellajetski'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ]);
    register_sidebar([
        'name'          => __('After Footer (Full Width)', 'marbellajetski'),
        'id'            => 'above-footer',
        'description'   => __('Full width area after the footer. Good for CTAs or newsletter signup.', 'marbellajetski'),
        'before_widget' => '<div id="%1$s" class="above-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
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

    // Jet ski animation (front page only)
    if (is_front_page() && file_exists(get_template_directory() . '/assets/js/jetski-anim.js')) {
        wp_enqueue_script('mjsk-jetski-anim', $uri . '/assets/js/jetski-anim.js', ['mjsk-main'], $v, true);
    }

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

    // ── Price & content placeholder replacement ──
    // All {{placeholder}} tokens in the HTML are replaced with Customizer values
    $defaults = mjsk_defaults();
    foreach ($defaults as $key => $default) {
        $placeholder = '{{' . $key . '}}';
        if (strpos($content, $placeholder) !== false) {
            $content = str_replace($placeholder, esc_html(mjsk_get($key)), $content);
        }
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
//  ALL CUSTOMIZER DEFAULTS (single source of truth)
// ============================================================
function mjsk_defaults() {
    return [
        // ── Contact ──
        'mjsk_phone'         => '+34 655 442 232',
        'mjsk_email'         => 'jetskimarbella@gmail.com',
        'mjsk_whatsapp'      => '34655442232',
        'mjsk_address'       => 'Playa de las Dunas, 29604 Marbella, Málaga',
        'mjsk_hours'         => 'Daily: 11am - 8pm',

        // ── Social ──
        'mjsk_facebook'      => 'https://www.facebook.com/jetskimarbella/',
        'mjsk_instagram'     => 'https://www.instagram.com/jetskimarbella/',
        'mjsk_tiktok'        => 'https://www.tiktok.com/@jetskimarbella',
        'mjsk_youtube'       => 'https://www.youtube.com/@marbellajetski',
        'mjsk_tripadvisor'   => 'https://www.tripadvisor.es/Attraction_Review-g187439-d6949698-Reviews-Marbella_Jet_Ski-Marbella_Costa_del_Sol_Province_of_Malaga_Andalucia.html',

        // ── Promo Banner ──
        'mjsk_promo_enabled' => true,
        'mjsk_promo_title'   => 'Book Before Summer & Save 10%!',
        'mjsk_promo_text'    => 'Early bird discount on all jet ski & yacht bookings for June–September 2026',

        // ── Hero ──
        'mjsk_hero_line1'       => 'Experience the',
        'mjsk_hero_highlight'   => 'Ultimate Thrill',
        'mjsk_hero_line2'       => 'on the Mediterranean',
        'mjsk_hero_subtitle' => 'Premium Jet Ski Rentals · Luxury Yacht Charters · Water Sports Adventures',
        'mjsk_season_text'   => 'Summer 2026 bookings now open! Secure your dates early.',
        'mjsk_stat_established' => '1998',
        'mjsk_stat_activities'  => '15',
        'mjsk_stat_rating'      => '4.9/5 ★',
        'mjsk_review_count'     => '500+',

        // ── Service Card "From" Prices ──
        'mjsk_card_jetski_from'      => '€70',
        'mjsk_card_jetski_unit'      => '20 min',
        'mjsk_card_yacht_from'       => '€250',
        'mjsk_card_yacht_unit'       => 'hour',
        'mjsk_card_watersports_from' => '€20',
        'mjsk_card_watersports_unit' => 'person',
        'mjsk_card_excursions_from'  => '€170',
        'mjsk_card_excursions_unit'  => 'hour',
        'mjsk_card_racing_from'      => '€299',
        'mjsk_card_racing_unit'      => 'session',

        // ── Service Card Descriptions ──
        'mjsk_desc_jetski'      => 'Experience the thrill on our latest Yamaha jet skis. Circuit rides or coastal excursions available.',
        'mjsk_desc_yachts'      => 'Cruise the coast in style. Captain, fuel, drinks and paddleboard included.',
        'mjsk_desc_watersports' => 'Wakeboarding, banana boats, crazy sofa, donuts & more for all ages!',
        'mjsk_desc_excursions'  => 'Explore Puerto Banús, Fuengirola and hidden coves with our expert guides.',
        'mjsk_desc_racing'      => 'Train with a professional racer. Circuit sessions, technique coaching & pure adrenaline.',

        // ── Jet Ski Circuit Prices ──
        'mjsk_jetski_circuit_20min' => '70',
        'mjsk_jetski_circuit_30min' => '90',
        'mjsk_jetski_circuit_60min' => '170',
        'mjsk_jetski_circuit_120min'=> '330',

        // ── Jet Ski Excursion Prices ──
        'mjsk_jetski_excursion_60min'  => '170',
        'mjsk_jetski_excursion_120min' => '330',

        // ── Water Sport Prices ──
        'mjsk_wakeboard_price'  => '90',
        'mjsk_waterski_price'   => '90',
        'mjsk_sofa_price'       => '20',
        'mjsk_waterbull_price'  => '20',
        'mjsk_banana_price'     => '20',
        'mjsk_airstream_price'  => '20',
        'mjsk_donut_price'      => '20',
        'mjsk_sup_price'        => '25',
        'mjsk_pedal_price'      => '30',
        'mjsk_kayak_price'      => '30',

        // ── Yacht Prices — Rinker 296 ──
        'mjsk_rinker_2h' => '400',
        'mjsk_rinker_3h' => '600',
        'mjsk_rinker_4h' => '800',
        'mjsk_rinker_6h' => '1,100',
        'mjsk_rinker_8h' => '1,400',

        // ── Yacht Prices — Cranchi 39 ──
        'mjsk_cranchi_2h' => '550',
        'mjsk_cranchi_3h' => '680',
        'mjsk_cranchi_4h' => '850',
        'mjsk_cranchi_6h' => '1,150',
        'mjsk_cranchi_8h' => '1,550',

        // ── Yacht Prices — Azimut 39 Fly ──
        'mjsk_azimut_2h' => '600',
        'mjsk_azimut_3h' => '800',
        'mjsk_azimut_4h' => '1,000',
        'mjsk_azimut_6h' => '1,500',
        'mjsk_azimut_8h' => '1,800',

        // ── Yacht Prices — Catamaran Bali 4.0 ──
        'mjsk_catamaran_2h' => '750',
        'mjsk_catamaran_3h' => '1,000',
        'mjsk_catamaran_4h' => '1,150',
        'mjsk_catamaran_6h' => '1,750',
        'mjsk_catamaran_8h' => '2,250',

        // ── Racing Lesson Prices ──
        'mjsk_racing_basic_price'        => '299',
        'mjsk_racing_intermediate_price' => '499',
        'mjsk_racing_masterclass_price'  => '699',

        // ── Misc ──
        'mjsk_photo_service_price'  => '25',
        'mjsk_lost_key_charge'      => '25',
        'mjsk_boat_deposit_rate'    => '20',
        'mjsk_standard_deposit_rate'=> '30',
    ];
}

/**
 * Get a customizer value with its default.
 */
function mjsk_get($key) {
    $defaults = mjsk_defaults();
    return get_theme_mod($key, $defaults[$key] ?? '');
}


// ============================================================
//  CUSTOMIZER — Editable settings in Appearance → Customize
// ============================================================
add_action('customize_register', function ($wp_customize) {

    // Helper to add a text field quickly
    $add_text = function ($id, $label, $section) use ($wp_customize) {
        $defaults = mjsk_defaults();
        $wp_customize->add_setting($id, [
            'default'           => $defaults[$id] ?? '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => $section,
            'type'    => 'text',
        ]);
    };

    $add_url = function ($id, $label, $section) use ($wp_customize) {
        $defaults = mjsk_defaults();
        $wp_customize->add_setting($id, [
            'default'           => $defaults[$id] ?? '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => $section,
            'type'    => 'url',
        ]);
    };

    // ── Panel: Site Settings ──
    $wp_customize->add_panel('mjsk_panel_site', [
        'title'    => __('Site Settings', 'marbellajetski'),
        'priority' => 30,
    ]);

    // ── Section: Contact Details ──
    $wp_customize->add_section('mjsk_contact', ['title' => __('Contact Details', 'marbellajetski'), 'panel' => 'mjsk_panel_site', 'priority' => 10]);
    $add_text('mjsk_phone',    'Phone Number', 'mjsk_contact');
    $add_text('mjsk_email',    'Email Address', 'mjsk_contact');
    $add_text('mjsk_whatsapp', 'WhatsApp Number (no +, no spaces)', 'mjsk_contact');
    $add_text('mjsk_address',  'Address', 'mjsk_contact');
    $add_text('mjsk_hours',    'Opening Hours', 'mjsk_contact');

    // ── Section: Social Media ──
    $wp_customize->add_section('mjsk_social', ['title' => __('Social Media Links', 'marbellajetski'), 'panel' => 'mjsk_panel_site', 'priority' => 20]);
    $add_url('mjsk_facebook',    'Facebook URL', 'mjsk_social');
    $add_url('mjsk_instagram',   'Instagram URL', 'mjsk_social');
    $add_url('mjsk_tiktok',      'TikTok URL', 'mjsk_social');
    $add_url('mjsk_youtube',     'YouTube URL', 'mjsk_social');
    $add_url('mjsk_tripadvisor', 'TripAdvisor URL', 'mjsk_social');

    // ── Section: Promo Banner ──
    $wp_customize->add_section('mjsk_promo', ['title' => __('Promo Banner', 'marbellajetski'), 'panel' => 'mjsk_panel_site', 'priority' => 30]);
    $wp_customize->add_setting('mjsk_promo_enabled', ['default' => true, 'sanitize_callback' => 'wp_validate_boolean']);
    $wp_customize->add_control('mjsk_promo_enabled', ['label' => 'Show Promo Banner', 'section' => 'mjsk_promo', 'type' => 'checkbox']);
    $add_text('mjsk_promo_title', 'Promo Title', 'mjsk_promo');
    $add_text('mjsk_promo_text',  'Promo Subtitle', 'mjsk_promo');

    // ── Section: Hero & Branding ──
    $wp_customize->add_section('mjsk_hero', ['title' => __('Hero & Branding', 'marbellajetski'), 'panel' => 'mjsk_panel_site', 'priority' => 40]);
    $add_text('mjsk_hero_line1',         'Hero Line 1 (e.g. "Experience the")', 'mjsk_hero');
    $add_text('mjsk_hero_highlight',     'Hero Highlight Text (gradient, e.g. "Ultimate Thrill")', 'mjsk_hero');
    $add_text('mjsk_hero_line2',         'Hero Line 2 (e.g. "on the Mediterranean")', 'mjsk_hero');
    $add_text('mjsk_hero_subtitle',     'Hero Subtitle', 'mjsk_hero');
    $add_text('mjsk_season_text',       'Season Text (e.g. "Summer 2026 bookings now open!")', 'mjsk_hero');
    $add_text('mjsk_stat_established',  'Stat: Year Established', 'mjsk_hero');
    $add_text('mjsk_stat_activities',   'Stat: Number of Activities', 'mjsk_hero');
    $add_text('mjsk_stat_rating',       'Stat: Rating (e.g. "4.9/5 ★")', 'mjsk_hero');
    $add_text('mjsk_review_count',      'Review Count (e.g. "500+")', 'mjsk_hero');

    // ── Section: Service Card Descriptions ──
    $wp_customize->add_section('mjsk_service_cards', ['title' => __('Service Card Text', 'marbellajetski'), 'panel' => 'mjsk_panel_site', 'priority' => 50]);
    $add_text('mjsk_card_jetski_from',      'Jet Ski "From" Price (e.g. €70)', 'mjsk_service_cards');
    $add_text('mjsk_card_jetski_unit',      'Jet Ski Unit (e.g. 20 min)', 'mjsk_service_cards');
    $add_text('mjsk_card_yacht_from',       'Yacht "From" Price (e.g. €250)', 'mjsk_service_cards');
    $add_text('mjsk_card_yacht_unit',       'Yacht Unit (e.g. hour)', 'mjsk_service_cards');
    $add_text('mjsk_card_watersports_from', 'Water Sports "From" Price (e.g. €20)', 'mjsk_service_cards');
    $add_text('mjsk_card_watersports_unit', 'Water Sports Unit (e.g. person)', 'mjsk_service_cards');
    $add_text('mjsk_card_excursions_from',  'Excursions "From" Price (e.g. €170)', 'mjsk_service_cards');
    $add_text('mjsk_card_excursions_unit',  'Excursions Unit (e.g. hour)', 'mjsk_service_cards');
    $add_text('mjsk_card_racing_from',      'Racing "From" Price (e.g. €299)', 'mjsk_service_cards');
    $add_text('mjsk_card_racing_unit',      'Racing Unit (e.g. session)', 'mjsk_service_cards');
    $add_text('mjsk_desc_jetski',           'Jet Ski Card Description', 'mjsk_service_cards');
    $add_text('mjsk_desc_yachts',           'Yacht Card Description', 'mjsk_service_cards');
    $add_text('mjsk_desc_watersports',      'Water Sports Card Description', 'mjsk_service_cards');
    $add_text('mjsk_desc_excursions',       'Excursions Card Description', 'mjsk_service_cards');
    $add_text('mjsk_desc_racing',           'Racing Card Description', 'mjsk_service_cards');

    // ── Panel: Pricing ──
    $wp_customize->add_panel('mjsk_panel_prices', [
        'title'    => __('Pricing (All Services)', 'marbellajetski'),
        'priority' => 31,
    ]);

    // ── Section: Jet Ski Circuit ──
    $wp_customize->add_section('mjsk_prices_circuit', ['title' => __('Jet Ski Circuit Prices', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 10]);
    $add_text('mjsk_jetski_circuit_20min',  '20 min (€)', 'mjsk_prices_circuit');
    $add_text('mjsk_jetski_circuit_30min',  '30 min (€)', 'mjsk_prices_circuit');
    $add_text('mjsk_jetski_circuit_60min',  '1 hour (€)', 'mjsk_prices_circuit');
    $add_text('mjsk_jetski_circuit_120min', '2 hours (€)', 'mjsk_prices_circuit');

    // ── Section: Jet Ski Excursions ──
    $wp_customize->add_section('mjsk_prices_excursion', ['title' => __('Jet Ski Excursion Prices', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 20]);
    $add_text('mjsk_jetski_excursion_60min',  '1 hour (€)', 'mjsk_prices_excursion');
    $add_text('mjsk_jetski_excursion_120min', '2 hours (€)', 'mjsk_prices_excursion');

    // ── Section: Water Sports ──
    $wp_customize->add_section('mjsk_prices_watersports', ['title' => __('Water Sport Prices', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 30]);
    $add_text('mjsk_wakeboard_price',  'Wakeboarding — 20 min (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_waterski_price',   'Water Skiing — 20 min (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_sofa_price',       'Crazy Sofa — per person (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_waterbull_price',  'Water Bull — per person (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_banana_price',     'Banana Boat — per person (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_airstream_price',  'Air Stream — per person (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_donut_price',      'Donut Ride — per person (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_sup_price',        'SUP — 1 hour (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_pedal_price',      'Pedal Boat — 1 hour (€)', 'mjsk_prices_watersports');
    $add_text('mjsk_kayak_price',      'Double Kayaks — 1 hour (€)', 'mjsk_prices_watersports');

    // ── Section: Yacht — Rinker 296 ──
    $wp_customize->add_section('mjsk_prices_rinker', ['title' => __('Yacht: Rinker 296 Captiva', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 40]);
    $add_text('mjsk_rinker_2h', '2 hours (€)', 'mjsk_prices_rinker');
    $add_text('mjsk_rinker_3h', '3 hours (€)', 'mjsk_prices_rinker');
    $add_text('mjsk_rinker_4h', '4 hours (€)', 'mjsk_prices_rinker');
    $add_text('mjsk_rinker_6h', '6 hours (€)', 'mjsk_prices_rinker');
    $add_text('mjsk_rinker_8h', '8 hours — Full Day (€)', 'mjsk_prices_rinker');

    // ── Section: Yacht — Cranchi 39 ──
    $wp_customize->add_section('mjsk_prices_cranchi', ['title' => __('Yacht: Cranchi 39', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 50]);
    $add_text('mjsk_cranchi_2h', '2 hours (€)', 'mjsk_prices_cranchi');
    $add_text('mjsk_cranchi_3h', '3 hours (€)', 'mjsk_prices_cranchi');
    $add_text('mjsk_cranchi_4h', '4 hours (€)', 'mjsk_prices_cranchi');
    $add_text('mjsk_cranchi_6h', '6 hours (€)', 'mjsk_prices_cranchi');
    $add_text('mjsk_cranchi_8h', '8 hours — Full Day (€)', 'mjsk_prices_cranchi');

    // ── Section: Yacht — Azimut 39 Fly ──
    $wp_customize->add_section('mjsk_prices_azimut', ['title' => __('Yacht: Azimut 39 Fly', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 60]);
    $add_text('mjsk_azimut_2h', '2 hours (€)', 'mjsk_prices_azimut');
    $add_text('mjsk_azimut_3h', '3 hours (€)', 'mjsk_prices_azimut');
    $add_text('mjsk_azimut_4h', '4 hours (€)', 'mjsk_prices_azimut');
    $add_text('mjsk_azimut_6h', '6 hours (€)', 'mjsk_prices_azimut');
    $add_text('mjsk_azimut_8h', '8 hours — Full Day (€)', 'mjsk_prices_azimut');

    // ── Section: Yacht — Catamaran Bali 4.0 ──
    $wp_customize->add_section('mjsk_prices_catamaran', ['title' => __('Yacht: Catamaran Bali 4.0', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 70]);
    $add_text('mjsk_catamaran_2h', '2 hours (€)', 'mjsk_prices_catamaran');
    $add_text('mjsk_catamaran_3h', '3 hours (€)', 'mjsk_prices_catamaran');
    $add_text('mjsk_catamaran_4h', '4 hours (€)', 'mjsk_prices_catamaran');
    $add_text('mjsk_catamaran_6h', '6 hours (€)', 'mjsk_prices_catamaran');
    $add_text('mjsk_catamaran_8h', '8 hours — Full Day (€)', 'mjsk_prices_catamaran');

    // ── Section: Racing Lessons ──
    $wp_customize->add_section('mjsk_prices_racing', ['title' => __('Racing Lesson Prices', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 80]);
    $add_text('mjsk_racing_basic_price',        'Basic Lesson — 30 min (€)', 'mjsk_prices_racing');
    $add_text('mjsk_racing_intermediate_price',  'Racing Experience — 1 hour (€)', 'mjsk_prices_racing');
    $add_text('mjsk_racing_masterclass_price',   'Racing Masterclass — 2 hours (€)', 'mjsk_prices_racing');

    // ── Section: Misc Pricing ──
    $wp_customize->add_section('mjsk_prices_misc', ['title' => __('Other Pricing & Rates', 'marbellajetski'), 'panel' => 'mjsk_panel_prices', 'priority' => 90]);
    $add_text('mjsk_photo_service_price',   'Photo Service Price (€)', 'mjsk_prices_misc');
    $add_text('mjsk_lost_key_charge',       'Lost Key Charge (€)', 'mjsk_prices_misc');
    $add_text('mjsk_boat_deposit_rate',     'Yacht Deposit Rate (%)', 'mjsk_prices_misc');
    $add_text('mjsk_standard_deposit_rate', 'Standard Activity Deposit Rate (%)', 'mjsk_prices_misc');
});


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
        "email": "<?php echo esc_attr(mjsk_get('mjsk_email')); ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo esc_attr(mjsk_get('mjsk_address')); ?>",
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
        "aggregateRating": { "@type": "AggregateRating", "ratingValue": "4.9", "reviewCount": "<?php echo esc_attr(mjsk_get('mjsk_review_count')); ?>" },
        "founder": { "@type": "Person", "name": "Daniel Stiers", "jobTitle": "Founder & Pro Racing Champion" },
        "foundingDate": "<?php echo esc_attr(mjsk_get('mjsk_stat_established')); ?>",
        "sameAs": [
            "<?php echo esc_url(mjsk_get('mjsk_facebook')); ?>",
            "<?php echo esc_url(mjsk_get('mjsk_instagram')); ?>",
            "<?php echo esc_url(mjsk_get('mjsk_tiktok')); ?>",
            "<?php echo esc_url(mjsk_get('mjsk_youtube')); ?>",
            "<?php echo esc_url(mjsk_get('mjsk_tripadvisor')); ?>"
        ]
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
