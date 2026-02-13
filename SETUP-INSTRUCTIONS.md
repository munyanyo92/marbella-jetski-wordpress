# Marbella JetSki — WordPress Theme Setup Guide

## What You're Getting

A complete WordPress theme that replicates the existing static website exactly. The design, animations, booking form, and all functionality are preserved. The WP admin lets you manage:

- **Navigation menus** — Appearance → Menus
- **Contact details** — Appearance → Customize → Contact Details
- **Social links** — Appearance → Customize → Social Media Links
- **Promo banner** — Appearance → Customize → Promo Banner (toggle on/off, change text)
- **Footer menus** — Appearance → Menus (Quick Links + Information)
- **Page content** — Edit HTML files in `page-content/` folder (or hire someone)
- **Multi-language** — Install Polylang plugin, then duplicate each page per language

---

## Requirements

| What | Minimum |
|------|---------|
| WordPress | 6.0+ |
| PHP | 7.4+ |
| MySQL/MariaDB | 5.7+ / 10.3+ |
| Hosting | Any WP-compatible host (Bluehost, SiteGround, Cloudways, etc.) |

### Recommended Plugins (all free)
- **Polylang** — Multi-language support (EN, ES, FR, NL)
- **WP Super Cache** or **LiteSpeed Cache** — Page caching for speed
- **Yoast SEO** — SEO management
- **UpdraftPlus** — Automated backups
- **WP Mail SMTP** — Reliable email delivery
- **Cookie Notice** — GDPR cookie consent banner

---

## Installation Steps

### Step 1: Build the Theme

On your computer (or Daniel's machine where the static site files exist):

```bash
cd /path/to/marbellajetski
bash wordpress-theme/build-theme.sh
```

This creates `wordpress-theme/marbellajetski.zip` — a complete, ready-to-install WordPress theme with all assets and content extracted.

### Step 2: Install WordPress

Install WordPress on your hosting provider (most hosts have 1-click installers).

### Step 3: Upload & Activate the Theme

1. Go to **WP Admin → Appearance → Themes → Add New → Upload Theme**
2. Upload `marbellajetski.zip`
3. Click **Activate**

### Step 4: Create Pages

Create these pages in **WP Admin → Pages → Add New**:

| Page Title | Slug | Template to Assign |
|------------|------|--------------------|
| Home | `home` | *(default — uses front-page.php)* |
| Booking | `booking` | Booking |
| Lessons | `lessons` | Lessons |
| About Us | `about-us` | About Us |
| Terms & Conditions | `terms` | Terms & Conditions |
| Weather Policy | `weather-policy` | Weather Policy |

**To assign a template:**
1. Edit the page
2. In the right sidebar, find **"Page Attributes"** → **"Template"**
3. Select the matching template from the dropdown
4. Publish

### Step 5: Set Homepage

1. Go to **Settings → Reading**
2. Select **"A static page"**
3. Set **Homepage** to the "Home" page you created
4. Save

### Step 6: Set Up Navigation Menu

1. Go to **Appearance → Menus**
2. Create a menu called "Primary Navigation"
3. Add the pages you created + any custom links (like anchor links to `/#services`, `/#boats`, etc.)
4. Assign it to the **"Primary Navigation (Header)"** location
5. Save

**Recommended menu items:**
- Home → `/`
- Services → `/#services` (custom link)
- Jet Ski → `/#jetski` (custom link)
- Water Sports → `/#watersports` (custom link)
- Yachts → `/#boats` (custom link)
- Racing → `/#racing-lessons` (custom link)
- Lessons → select "Lessons" page
- About Us → select "About Us" page
- Contact → `/#contact` (custom link)

### Step 7: Configure Contact & Social

1. Go to **Appearance → Customize**
2. **Contact Details** — Update phone, email, WhatsApp, address, hours
3. **Social Media Links** — Update Facebook, Instagram, TikTok, YouTube, TripAdvisor URLs
4. **Promo Banner** — Toggle on/off, change the promotional text
5. Click **Publish**

### Step 8: Set Up Permalinks

1. Go to **Settings → Permalinks**
2. Select **"Post name"** (e.g. `yoursite.com/booking/`)
3. Save

---

## Multi-Language Setup (Polylang)

### Install Polylang
1. Go to **Plugins → Add New**
2. Search "Polylang"
3. Install & Activate

### Configure Languages
1. Go to **Languages → Languages**
2. Add: English (en), Español (es), Français (fr), Nederlands (nl)
3. Set English as default

### Translate Pages
1. For each English page, click **"+"** in the translation column
2. Create the translated version (copy the page-content HTML and translate its text)
3. Polylang automatically handles URL structure (`/es/booking/`, `/fr/`, etc.)

Once Polylang is active, the theme's language switcher in the header automatically uses Polylang's switcher instead of the static links.

---

## Updating Content

### Easy Updates (No Code Needed)
These are all done through the WP Admin dashboard:
- **Phone, email, WhatsApp** → Appearance → Customize → Contact Details
- **Social media links** → Appearance → Customize → Social Media Links
- **Promo banner text** → Appearance → Customize → Promo Banner
- **Navigation links** → Appearance → Menus
- **Footer links** → Appearance → Menus (assign to footer locations)

### Content Updates (Edit HTML Files)
To change page content (prices, descriptions, images):

1. Connect to your server via FTP or File Manager
2. Navigate to `wp-content/themes/marbellajetski/page-content/`
3. Edit the relevant HTML file:
   - `home.html` — Homepage sections (hero, services, jet ski, yachts, etc.)
   - `booking.html` — Booking form content
   - `lessons.html` — Lessons page
   - `about-us.html` — About Us page
   - `terms.html` — Legal pages
   - `weather-policy.html` — Weather policy
4. The HTML has the same structure as the original static site
5. Save and refresh the page

### Image Updates
- Upload new images to `wp-content/themes/marbellajetski/assets/media/photos/`
- Update the `src=""` attributes in the `page-content/` HTML files to match

---

## Theme File Structure

```
marbellajetski/
├── style.css                    ← WP theme identifier
├── functions.php                ← Core: menus, scripts, customizer, helpers
├── header.php                   ← Shared header: <head>, nav menu, promo
├── footer.php                   ← Shared footer: contact, links, WhatsApp
├── front-page.php               ← Homepage template
├── index.php                    ← Fallback template
├── page.php                     ← Default page (WP editor content)
├── 404.php                      ← 404 error page
│
├── page-templates/              ← Selectable page templates
│   ├── template-booking.php
│   ├── template-lessons.php
│   ├── template-about.php
│   ├── template-terms.php
│   └── template-weather.php
│
├── page-content/                ← Static HTML content (auto-generated)
│   ├── home.html                ← Homepage body content
│   ├── home-modal.html          ← Boat detail modal
│   ├── booking.html             ← Booking form content
│   ├── booking-styles.html      ← Booking page styles
│   ├── lessons.html             ← Lessons content
│   ├── lessons-styles.html      ← Lessons page styles
│   ├── about-us.html            ← About page content
│   ├── terms.html               ← Terms content
│   └── weather-policy.html      ← Weather policy content
│
└── assets/                      ← All static assets
    ├── css/main.css             ← Full site CSS
    ├── js/script.js             ← Full site JavaScript
    └── media/                   ← Photos, videos, racing images
        ├── photos/
        ├── videos/
        └── racing/
```

### How It Works
1. `header.php` + `footer.php` are shared across ALL pages (nav, footer, scripts)
2. Each page template calls `mjsk_load_page_content('filename.html')`
3. The helper function loads the HTML from `page-content/` and **automatically rewrites** all local asset paths (`assets/...`) to WordPress theme URIs
4. Internal page links (`booking.html`) are rewritten to WordPress slugs (`/booking/`)
5. The navigation uses `wp_nav_menu()` so it's fully editable in WP Admin

---

## Booking Form

The booking form uses a custom JavaScript solution that generates a WhatsApp message with the booking details. **It does NOT use WordPress form plugins** — it works exactly as the static site version. The WhatsApp number is hardcoded in the booking form JavaScript; to change it, edit the `page-content/booking.html` file.

To switch to a proper WordPress booking system later, replace the form in `page-content/booking.html` with a shortcode from a plugin like:
- **Amelia** (free booking plugin)
- **Bookly** (appointment scheduling)
- **WooCommerce Bookings** (paid, most powerful)

---

## FAQ

**Q: Can I change prices on the website?**
A: Yes. Edit the HTML files in `page-content/`. Search for the price amount (e.g., `€70`) and change it.

**Q: Can I add new pages?**
A: Yes! Create a new page in WP Admin → Pages → Add New. For pages with custom design, create an HTML file in `page-content/` and a matching template in `page-templates/`.

**Q: What about the videos?**
A: Videos are in `assets/media/videos/` and `assets/media/racing/`. They're referenced in the HTML. Upload new ones to the same folder and update the HTML.

**Q: How do I update/rebuild the theme from the static site?**
A: Run `bash wordpress-theme/build-theme.sh` again. It will regenerate all content files and the ZIP.

**Q: Can I use this with page builders like Elementor?**
A: The theme doesn't require page builders, but you can install Elementor and create new pages with it. The existing pages use their own templates.
