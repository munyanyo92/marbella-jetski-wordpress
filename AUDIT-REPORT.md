# Marbella JetSki WordPress Theme — Audit Report

**Auditor:** Automated Code Audit  
**Date:** 13 February 2026  
**Scope:** `/wordpress-theme/marbellajetski/`

---

## CHECK 1: Hardcoded Prices

**Result: PASS**

All `€` / `&euro;` price values in `page-content/*.html` files are wrapped in `{{mjsk_xxx}}` placeholders. The only bare `€0` occurrences are JavaScript default placeholder text in the booking form UI (deposit amounts, totals) that get overwritten dynamically at runtime — these are correct.

---

## CHECK 2: Hardcoded Contact Info

**Result: PASS** (with minor notes)

- **Phone numbers:** No hardcoded phone numbers found outside placeholders. All use `{{mjsk_phone}}` or `{{mjsk_whatsapp}}`.
- **Emails:** No hardcoded email addresses outside placeholders. All use `{{mjsk_email}}`.
- **WhatsApp:** All WhatsApp links use `{{mjsk_whatsapp}}`.

**WARN:** `booking.html` line 470 — `placeholder="+34 XXX XXX XXX"` is a form input placeholder string, not actual contact data. Acceptable.

**WARN:** `tel:+{{mjsk_whatsapp}}` pattern is used in multiple files (booking.html L361, L377, L591; home.html L1392, L1567; terms.html L40, L153). The `mjsk_whatsapp` default is `34655442232` (no `+`), so `tel:+34655442232` is valid. However this means the phone `tel:` link uses the WhatsApp number rather than the `mjsk_phone` value. Currently they happen to match, but semantically `tel:` should use `mjsk_phone` (which includes spaces). Not a bug today but fragile if the numbers ever diverge.

---

## CHECK 3: Hardcoded Social URLs

**Result: PASS**

All social links in page-content HTML files use placeholders:
- `{{mjsk_facebook}}`, `{{mjsk_instagram}}`, `{{mjsk_tiktok}}`, `{{mjsk_youtube}}`, `{{mjsk_tripadvisor}}`

The only plain-text mentions of "TripAdvisor" are icon labels in testimonial cards (e.g., `<i class="fab fa-tripadvisor"></i> TripAdvisor`) — these are display text, not URLs. Clean.

**WARN:** The contact section `social-follow` div in `home.html` (line ~1618-1633) only includes Facebook, Instagram, and TripAdvisor. **TikTok and YouTube are missing** from this specific social group (but are present in the gallery section and in `footer.php`). Inconsistent but not broken.

---

## CHECK 4: External Domain References

**Result: PASS** (all acceptable)

| File | Line | Reference | Verdict |
|------|------|-----------|---------|
| `booking.html` | 1083 | `'Sent from marbellajetski.com booking system'` | OK — WhatsApp message text |
| `terms.html` | 42 | `<a href="index.html">www.marbellajetski.com</a>` | OK — display text, href rewritten to WP home by `page_map` |
| `style.css` | 3,5 | `Theme URI / Author URI: https://marbellajetski.com` | OK — standard WP theme header |

No problematic external domain references found.

---

## CHECK 5: Placeholder Integrity

### 5a. Placeholders used in HTML (48 unique)

All 48 placeholders found in `page-content/*.html` files have a corresponding key in `mjsk_defaults()`. **No orphaned HTML placeholders.**

### 5b. Keys in `mjsk_defaults()` not used in any HTML file (20 keys)

| Key | Used In | Verdict |
|-----|---------|---------|
| `mjsk_promo_enabled` | `header.php` (PHP) | OK — used in PHP |
| `mjsk_promo_title` | `header.php` (PHP) | OK — used in PHP |
| `mjsk_promo_text` | `header.php` (PHP) | OK — used in PHP |
| `mjsk_review_count` | `functions.php` JSON-LD | OK — used in PHP |
| `mjsk_contact` | Customizer section ID | OK — not a data key |
| `mjsk_hero` | Customizer section ID | OK — not a data key |
| `mjsk_social` | Customizer section ID | OK — not a data key |
| `mjsk_promo` | Customizer section ID | OK — not a data key |
| `mjsk_service_cards` | Customizer section ID | OK — not a data key |
| `mjsk_panel_site` | Customizer panel ID | OK — not a data key |
| `mjsk_panel_prices` | Customizer panel ID | OK — not a data key |
| `mjsk_prices_circuit` | Customizer section ID | OK — not a data key |
| `mjsk_prices_excursion` | Customizer section ID | OK — not a data key |
| `mjsk_prices_watersports` | Customizer section ID | OK — not a data key |
| `mjsk_prices_rinker` | Customizer section ID | OK — not a data key |
| `mjsk_prices_cranchi` | Customizer section ID | OK — not a data key |
| `mjsk_prices_azimut` | Customizer section ID | OK — not a data key |
| `mjsk_prices_catamaran` | Customizer section ID | OK — not a data key |
| `mjsk_prices_racing` | Customizer section ID | OK — not a data key |
| `mjsk_prices_misc` | Customizer section ID | OK — not a data key |

**Result: PASS** — All data keys are used. Section/panel IDs are correctly not in `mjsk_defaults()` (they appear in the grep because they're string literals in `functions.php` used for `add_section`/`add_panel` calls, not as defaults keys).

---

## CHECK 6: PHP Template Completeness

### 6a. Template Coverage

| HTML Content File | Template | Loads via `mjsk_load_page_content()` | Status |
|---|---|---|---|
| `home.html` | `front-page.php` | ✅ | PASS |
| `home-modal.html` | `front-page.php` | ✅ | PASS |
| `booking.html` | `template-booking.php` | ✅ | PASS |
| `booking-styles.html` | `template-booking.php` | ✅ | PASS |
| `lessons.html` | `template-lessons.php` | ✅ | PASS |
| `lessons-styles.html` | `template-lessons.php` | ✅ | PASS |
| `about-us.html` | `template-about.php` | ✅ | PASS |
| `about-us-styles.html` | `template-about.php` | ✅ | PASS |
| `terms.html` | `template-terms.php` | ✅ | PASS |
| `terms-styles.html` | `template-terms.php` | ✅ | PASS |
| `weather-policy.html` | `template-weather.php` | ✅ | PASS |
| `weather-policy-styles.html` | `template-weather.php` | ✅ | PASS |

**Result: PASS** — Every content HTML file has a corresponding template.

### 6b. `mjsk_load_page_content()` Function

- ✅ Handles asset path rewriting (`src="assets/..."` → theme URI)
- ✅ Has `page_map` for internal `.html` → WP slug link rewriting
- ✅ Iterates `mjsk_defaults()` for placeholder replacement
- ✅ Uses `esc_html()` on replaced values

**FAIL:** `boats/*.html` links (e.g., `href="boats/rinker-296.html"`) are NOT in the `page_map` and therefore **won't be rewritten**. Found in `home.html` lines 695, 727, 759, 791. However, these links all have `onclick="openBoatModal('...');return false;"` which prevents navigation, so the broken href is cosmetically bad but functionally harmless since the JS modal intercepts the click.

### 6c. PHP Syntax

**Result: PASS** — All PHP files reviewed. No unclosed tags, mismatched brackets, or syntax issues found.

---

## CHECK 7: Asset Integrity

### 7a. Missing Files

**FAIL:** `assets/media/racing/race-start-1.mp4` — Referenced in `lessons.html` line ~350 but does **NOT exist** in the assets directory.

### 7b. External Media URLs

| File | URL | Verdict |
|---|---|---|
| `home.html` L6 | Pexels video (4115690) | WARN — External CDN video. Works but adds external dependency. |
| `about-us.html` L5 | Vimeo video (368763066) | WARN — External CDN video. Same concern. |

### 7c. All Other Asset References

**Result: PASS** — All other `src="assets/..."` image and video paths resolve to existing files.

---

## CHECK 8: Customizer Registration

**Result: PASS**

Every key in `mjsk_defaults()` that is a data value (not a section/panel ID) has:
- ✅ `add_setting()` with `sanitize_callback`
- ✅ `add_control()` in the appropriate section

Sanitize callbacks:
- Text fields: `sanitize_text_field` ✅
- URL fields (social links): `esc_url_raw` ✅
- Boolean (promo_enabled): `wp_validate_boolean` ✅

**WARN:** Price fields use `sanitize_text_field` rather than a numeric sanitizer. This means someone could enter non-numeric text in a price field. Current defaults contain commas (e.g., `'1,100'`), so a strict numeric sanitizer would break those. The current approach is pragmatic but worth noting.

---

## CHECK 9: wp_enqueue Checks

### 9a. Local Files

| Enqueued Handle | File | Exists? |
|---|---|---|
| `mjsk-main` (CSS) | `assets/css/main.css` | ✅ |
| `mjsk-main` (JS) | `assets/js/script.js` | ✅ |
| `mjsk-jetski-anim` | `assets/js/jetski-anim.js` | ✅ (conditional: front page only, with `file_exists` check) |

### 9b. CDN Libraries

| Library | CDN URL | Status |
|---|---|---|
| Google Fonts | `fonts.googleapis.com` | ✅ Properly enqueued |
| Font Awesome 6.5.1 | `cdnjs.cloudflare.com` | ✅ Properly enqueued |
| AOS 2.3.1 CSS | `unpkg.com` | ✅ Properly enqueued |
| AOS 2.3.1 JS | `unpkg.com` | ✅ Properly enqueued with `true` (footer) |
| Swiper 11 CSS | `cdn.jsdelivr.net` | ✅ Properly enqueued |
| Swiper 11 JS | `cdn.jsdelivr.net` | ✅ Properly enqueued with correct dependency |

**Result: PASS** — All enqueued files exist and CDN libs are correctly loaded.

---

## CHECK 10: Miscellaneous

### 10a. TODO / FIXME / HACK Comments

**Result: PASS** — None found in any theme file.

### 10b. console.log Statements

**WARN:** Multiple `console.log` statements found in JS files:

- `assets/js/jetski-anim.js` L19, L454 — Debug logging (`[jetski] v6 ultra-real loaded/running`)
- `assets/js/script.js` — 15+ instances of `console.log` for error handling, SW registration, weather fetch, and an ASCII art banner (L855)

These are typical for development but should ideally be removed or wrapped in a debug flag for production. **Not blocking** but worth cleaning up.

### 10c. screenshot.png

**Result: PASS** — `screenshot.png` exists (5,611 bytes).

### 10d. style.css Theme Header

**Result: PASS** — Complete and valid WP theme header with:
- Theme Name, URI, Author, Description, Version (1.0.0)
- Requires at least: 6.0, Tested up to: 6.5, Requires PHP: 7.4
- License, Text Domain, Tags

### 10e. SETUP-INSTRUCTIONS.md Accuracy

**Result: PASS** — Comprehensive and accurate. Covers:
- Build process, installation, page creation with correct slugs/templates
- Menu setup, Customizer configuration, price editing
- Polylang multi-language setup
- Content editing instructions with correct file paths
- Correctly warns not to hardcode prices over placeholders

---

## CRITICAL BUGS FOUND

### BUG 1: Yacht Accordion Display Prices Wrong in booking.html

**Severity: HIGH — Customers see wrong prices**

In `booking.html`, every yacht accordion shows **mismatched display prices**. The `data-price` attribute (used for calculation) is correct, but the visible `<span class="price">` shows the **wrong tier's price**.

**Pattern:** Each yacht's accordion body shows the price from ONE TIER LOWER than the actual duration:

**Azimut 39 Fly (booking.html ~L196-220):**
| Duration | `data-price` (correct) | Display `<span class="price">` (WRONG) |
|---|---|---|
| 2h | `{{mjsk_azimut_2h}}` | `€{{mjsk_azimut_2h}}` ✅ |
| 3h | `{{mjsk_azimut_3h}}` | `€{{mjsk_azimut_2h}}` ❌ shows 2h price |
| 4h | `{{mjsk_azimut_4h}}` | `€{{mjsk_azimut_3h}}` ❌ shows 3h price |
| 6h | `{{mjsk_azimut_6h}}` | `€{{mjsk_azimut_4h}}` ❌ shows 4h price |
| 8h | `{{mjsk_azimut_8h}}` | `€{{mjsk_azimut_6h}}` ❌ shows 6h price |

**Same bug exists for:** Cranchi 39, Rinker 296, and Catamaran Bali 4.0 — all four yachts have the identical off-by-one display bug. The `data-price` (used for actual calculation) is correct, but the visual price the user sees is wrong.

### BUG 2: Missing Jet Ski Excursion 2h Option in booking.html

**Severity: MEDIUM**

`home.html` offers a 2-hour jet ski excursion (`jetski-excursion-120` at `€{{mjsk_jetski_excursion_120min}}`), but `booking.html` only lists the 1-hour excursion (`jetski-excursion-60`). A customer clicking "Book Excursion" from the homepage for 2 hours has no corresponding option on the booking page.

### BUG 3: Missing Kayak Option in booking.html

**Severity: MEDIUM**

`home.html` shows Double Kayaks (`€{{mjsk_kayak_price}}`) as a bookable activity, but `booking.html` has **no kayak entry** in the activity list. Customers cannot book kayaks through the booking form.

### BUG 4: Missing Asset — race-start-1.mp4

**Severity: LOW**

`lessons.html` references `assets/media/racing/race-start-1.mp4` but this file does not exist. The video element will show nothing for this card.

---

## WARNINGS SUMMARY

| # | Issue | File(s) | Severity |
|---|---|---|---|
| W1 | `tel:` links use `mjsk_whatsapp` instead of `mjsk_phone` | booking.html, home.html, terms.html | Low |
| W2 | Contact section social-follow missing TikTok + YouTube | home.html L1618 | Low |
| W3 | `boats/*.html` hrefs not in `page_map` (mitigated by JS modal) | home.html L695,727,759,791 | Low |
| W4 | `console.log` statements in production JS | jetski-anim.js, script.js | Low |
| W5 | External video dependencies (Pexels, Vimeo CDN) | home.html, about-us.html | Low |
| W6 | Price fields use `sanitize_text_field` not numeric sanitizer | functions.php | Info |
| W7 | `booking.html` ends with `</body>` tag (inside page-content, loaded within `<main>`) | booking.html L1095 | Low |

---

## FIXES NEEDED (Priority Order)

1. **BUG 1** — Fix yacht display prices in `booking.html` (all 4 yacht accordions — ~16 lines to fix)
2. **BUG 2** — Add `jetski-excursion-120` option to `booking.html`
3. **BUG 3** — Add kayak option to `booking.html`
4. **BUG 4** — Add `race-start-1.mp4` to assets or remove the reference
5. **W7** — Remove stray `</body>` tag from `booking.html`
