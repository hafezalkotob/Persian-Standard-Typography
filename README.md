```markdown
# Persian Standard Typography (PST)

A clean, modern, and fully Persian-first typography system for the web.  
Includes a complete design system, optimized Persian fonts, ready-to-use CSS tokens, dark mode support, and seamless integration with Elementor and WordPress.

**[مطالعه به فارسی](README-fa.md)**

---

## Why PST?

Most CSS frameworks treat Persian as an afterthought. PST is built **from the ground up for Persian script** — with carefully tuned font sizes, line heights, weights, and spacing that respect the unique characteristics of Persian typography.

- **Persian-first**: Every token and rule is designed for RTL Persian text.
- **No letter-spacing**: Unlike Latin typography, `letter-spacing` harms Persian readability. We enforce zero.
- **No justification**: `text-align: justify` creates awkward gaps in Persian. We avoid it entirely.
- **Optimized font stack**: IranSansX, IranYekanX, Abar (Low/Mid/High), Vazirmatn — all with proper `@font-face` declarations.

---

## Features

- 🎨 **Design tokens** — Colors, typography scale, spacing, and dark mode
- 🔤 **6 Persian font families** — 60+ weights across 4 typefaces
- 🌙 **Dark mode** — Automatic OS-level detection + manual toggle
- 📐 **Responsive typography** — Font sizes and spacing scale down on mobile
- 🧩 **Elementor integration** — PST fonts appear directly in Elementor's font list
- 🧰 **WordPress plugin** — Manage fonts, CDN, and custom CSS from a single admin panel
- ⚡ **CDN-ready** — All static assets optimized for CDN delivery

---

## Getting Started

### Via CDN
Add this line to your HTML `<head>`:
```html
<link rel="stylesheet" href="https://cdn.cdoc.ir/pst/css/main.css">
```
---

### Via WordPress Plugin
1. Download the `pst-manager` folder from `wordpress-plugin/`.
2. Upload it to `wp-content/plugins/`.
3. Activate "PST Manager" and go to **Settings → PST Manager**.
4. Select your fonts, set brand colors, and save.

---

## Project Structure

```
PST/
├── core/                   ← Core design system (CDN-ready)
│   ├── css/
│   │   ├── base/           ← Reset, tokens, typography, ui-base
│   │   ├── components/     ← Forms, tables
│   │   └── main.css        ← Entry point
│   ├── fonts/              ← Persian font files (woff/woff2)
│   ├── js/                 ← Theme toggle, table helpers
│   ├── img/                ← Placeholder images
│   └── index.html          ← Typography showcase
├── wordpress-plugin/       ← PST Manager WordPress plugin
│   └── pst-manager/
└── README.md
```

---

## Build for CDN

Run the build script to generate CDN-ready files with absolute paths:

```bash
node build-cdn.js
```

Output is written to `core/dist/cdn/`. Upload that folder to your CDN.

---

## License

This project is proprietary. To use the included fonts, you must have a valid license from their respective foundries (e.g., [fontiran.com](https://fontiran.com)).  
The CSS, JS, and design tokens are authored by **Parsa Hafezalkotob**.