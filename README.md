# Marbella JetSki — WordPress Theme

A complete WordPress theme for **Marbella JetSki** — Costa del Sol's premier water sports destination offering jet ski hire, luxury yacht charters, water sports experiences, and professional racing lessons. Family-owned since 1998.

## Features

- **Fully responsive** — Mobile, tablet, and desktop optimized
- **Multi-language** — English, Spanish, French & Dutch (via Polylang plugin)
- **WP Customizer** — Edit phone, email, WhatsApp, social links, promo banner from the dashboard
- **WP Nav Menus** — Navigation fully editable in Appearance → Menus
- **SEO ready** — JSON-LD structured data, semantic HTML, meta tags
- **Fast loading** — AOS animations, Swiper carousels, optimized assets
- **WhatsApp booking** — Integrated WhatsApp booking flow
- **Custom page templates** — Homepage, Booking, Lessons, About Us, Terms, Weather Policy

## Installation

1. Download or clone this repository
2. ZIP the `marbellajetski/` folder (or use the pre-built ZIP)
3. Go to **WP Admin → Appearance → Themes → Add New → Upload Theme**
4. Upload the ZIP and click **Activate**

## Setup

See **[SETUP-INSTRUCTIONS.md](SETUP-INSTRUCTIONS.md)** for the complete step-by-step guide including:

- Creating pages and assigning templates
- Setting up navigation menus
- Configuring contact details and social links
- Installing Polylang for multi-language support
- Updating content (prices, images, text)

## Theme Structure

```
marbellajetski/
├── style.css                  ← WP theme identifier
├── functions.php              ← Menus, scripts, customizer, helpers
├── header.php                 ← Shared nav, promo banner
├── footer.php                 ← Shared footer, WhatsApp, back-to-top
├── front-page.php             ← Homepage
├── page.php                   ← Default page template
├── 404.php                    ← Error page
├── page-templates/            ← Assignable page templates
├── page-content/              ← Static HTML content (auto-extracted)
└── assets/                    ← CSS, JS, photos, videos
```

## Tech Stack

- WordPress 6.0+
- PHP 7.4+
- Google Fonts (Montserrat, Playfair Display, Space Grotesk)
- Font Awesome 6.5.1
- AOS 2.3.1 (Animate on Scroll)
- Swiper 11 (Touch carousels)

## License

Proprietary — © STIERS E HIJOS S.L. (Marbella JetSki). All rights reserved.
