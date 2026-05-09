# Persian Standard Typography (PST)

A clean, modern, and Persian-first typography system for the web.  
PST includes a complete design system, optimized Persian fonts, ready-to-use CSS tokens, dark mode support, and seamless Elementor/WordPress integration.

**[مطالعه به فارسی](README-fa.md)**

---

## Why PST?

Most CSS frameworks treat Persian as an afterthought.  
PST is built **from the ground up for Persian script** — with carefully tuned font sizes, line heights, weights, and spacing tailored to Persian typography.

- **Persian-first:** Every token and rule is designed for RTL Persian text.
- **No letter-spacing:** In Persian typography, `letter-spacing` often reduces readability, so PST enforces zero.
- **No justification:** `text-align: justify` can create awkward spacing in Persian; PST avoids it.
- **Optimized font stack:** IranSansX, IranYekanX, Abar (Low/Mid/High), and Vazirmatn with proper `@font-face` declarations.

---

## Features

- 🎨 **Design tokens** — colors, typography scale, spacing, and dark mode
- 🔤 **6 Persian font families** — 60+ weights across 4 typefaces
- 🌙 **Dark mode** — automatic OS-level detection + manual toggle
- 📐 **Responsive typography** — scale-aware font sizes and spacing for mobile
- 🧩 **Elementor integration** — PST fonts appear in Elementor font controls
- 🧰 **WordPress plugin** — manage fonts, CDN, and custom CSS in one admin panel
- ⚡ **CDN-ready assets** — optimized static files for fast delivery

---

## Getting Started

### 1) Via CDN

Add this line to your HTML `<head>`:
```html
<link rel="stylesheet" href="https://cdn.cdoc.ir/pst/css/main.css">

### 2) Via WordPress Plugin

1. Download the `pst-manager` folder from `wordpress-plugin/`.
2. Upload it to `wp-content/plugins/`.
3. Activate **PST Manager**.
4. Go to **Settings → PST Manager**.
5. Select fonts, set brand colors, and save.

---

## Project Structure

text
PST/
├── core/                   # Core design system (CDN-ready)
│   ├── css/
│   │   ├── base/           # Reset, tokens, typography, UI base
│   │   ├── components/     # Forms, tables
│   │   └── main.css        # Entry point
│   ├── fonts/              # Persian font files (woff/woff2)
│   ├── js/                 # Theme toggle, table helpers
│   ├── img/                # Placeholder images
│   └── index.html          # Typography showcase
├── wordpress-plugin/       # PST Manager WordPress plugin
│   └── pst-manager/
└── README.md

---

## Build for CDN

Run the build script to generate CDN-ready files with absolute paths:

bash
node build-cdn.js

The output is generated in:

text
core/dist/cdn/

Upload that directory to your CDN origin.

---

## License

This project is proprietary.

To use the included fonts, you must hold valid licenses from their respective foundries (e.g., [fontiran.com](https://fontiran.com)).

The CSS, JS, and design tokens are authored by **Parsa Hafezalkotob**.


اگر بخواید، در مرحله بعد یک نسخه‌ی **خیلی حرفه‌ای‌تر** هم می‌دم که شامل این‌ها باشه:
- badges (نسخه، لایسنس، CDN status)
- Table of Contents خودکار
- بخش Browser Support
- Quick Demo / Screenshot section
- Contributing و Changelog links