# Premier Family Salon & Hair Spa Website

A modern, multi-page static website (HTML/CSS/JS + minimal PHP mail handler) suitable for deployment on typical shared hosting / cPanel environments.

## Pages
- `index.html` – Landing page with improved high‑quality hero image, featured services, testimonials placeholder, CTA.
- `services.html` – Structured service categories + NEW interactive image tile mosaic linking to Hair, Skin, Makeup, Hand & Feet anchors.
- `products.html` – Sample product gallery with placeholder images, categories & prices (grid cards upgraded with image + price tag).
- `contact.html` – Booking / inquiry form with honeypot anti-spam.
- `booking.php` – Server-side handler that validates and emails booking requests.

Assets in `assets/css`, `assets/js`, and `assets/img` (logo images now referenced directly in the header). Add / replace favicon & hero imagery as needed.

### Recent Simplification & Asset Consolidation

The site previously used multiple external (Pexels) images and a complex "Neon Mesh" backdrop. At your request this has been simplified so every hero / category section now reuses a single local image: `assets/img/background.jpeg`.

Key changes:
1. Removed external image URLs (hero, products, services, contact, category side imagery). A single variable `--hero-img` in `assets/css/styles.css` now controls all hero and category backgrounds.
2. Plain hero variant (`.hero--plain`) ensures the image is fully visible by disabling gradient overlays / mesh layers.
3. Category sections now set `--cat-img: var(--hero-img)` for consistent branding; pseudo-elements still provide subtle styling but source the same local asset.
4. To change the global image, edit only one line: in `:root` inside `styles.css` update `--hero-img: url('assets/img/background.jpeg');` to a new path.
5. All remote Pexels references were purged; no external image fetch occurs now (improved privacy + predictable load performance).

If you want to re‑enable the previous neon mesh or per‑page imagery, you can restore the earlier classes (`.hero-mesh`, `.hero--purple*`) and override `--hero-img` on specific pages.

### Previous UI Enhancements (Neon Theme Revamp)
1. Introduced dark neon aesthetic: violet (primary), hot magenta (secondary), peach glow (tertiary) with multi‑stop gradient `--grad-accent` applied to key interactive components.
2. Global background replaced by layered radial + conic glows for subtle ambient light; body uses multi-layer blend to avoid harsh banding.
3. Navigation underline, buttons (including WhatsApp connect), accent bars and price/metric text now use new gradient for cohesive brand energy.
4. Product / service / feature / testimonial cards updated with refined internal glow hovers (radial accent shift from gold to magenta/violet) and improved focus rings (`:focus-visible` now in magenta for distinct accessibility contrast).
5. Headings with gradient text migrate from gold to violet→magenta transitions; metrics and badges recolored for stronger visual hierarchy while preserving contrast ratios (AA for normal text on dark surfaces maintained).
6. Added ambient overlay pseudo-elements (`body::before/::after`) with blur to create depth without heavy images.
7. Adjusted form focus states to use secondary accent ensuring clearer visual feedback; updated card highlight glows to respect reduced motion preference.
8. Minor contrast tuning: background surfaces darkened slightly (`#0b0b11` base) while muted text lightened (`#b5b5c6`) for WCAG AA legibility at common sizes.

### (Legacy) Hero "Neon Mesh" Aura Layer
A layered gradient field (`.hero-mesh`) now sits behind the photographic hero background producing a soft 3D aura effect:

- Composition: stacked radial gradients (violet / magenta / peach) + two rotating conic gradients (pseudo-elements) with `mix-blend-mode: screen` and `overlay` for additive light.
- Motion: low-amplitude drift (`mesh-drift`) + ultra slow rotational energy (`mesh-rotate`), both GPU-friendly (transform only).
- Performance: Heavy blurs isolated to a single element; `will-change: transform` hint; no layout thrash.
- Accessibility: `prefers-reduced-motion` disables animations while retaining a static blurred glow field so contrast & brand feel remain.
- Non-intrusive: Marked `aria-hidden="true"` so assistive tech ignores decorative layer.

Basic overrides (in `:root` or a page-specific scope) to tune intensity:
```css
/* Soften overall glow */
.hero-mesh { filter: blur(32px) saturate(120%); }
/* Slow the drift */
@keyframes mesh-drift { 0% { transform: translate3d(-1%,0,0) scale(1); } 100% { transform: translate3d(1%,1%,0) scale(1.04); } }
/* Disable conic overlays for a flatter look */
.hero-mesh::before, .hero-mesh::after { display:none; }
```

Remove entirely (minimal hero) by deleting the `<div class="hero-mesh"></div>` in `index.html` or overriding:
```css
.hero-mesh { display:none; }
```

## Tech Stack
Pure static front-end (no build step) + lightweight PHP mail script.

## Deployment (cPanel)
1. Log into cPanel.
2. Open File Manager and navigate to `public_html` (or subfolder/domain root as needed).
3. Upload all project files preserving structure (or use FTP / Git if available).
4. Ensure `booking.php` has correct file permissions (0644 typical).
5. Edit `booking.php` and change:
   - `$TO_EMAIL` to your real booking destination email.
   - `From:` header domain to one authorized by your SPF (e.g. `noreply@yourdomain.com`).
6. Test the form:
   - Submit a booking from `contact.html`.
   - Confirm email arrives (check spam initially).
7. (Optional) Add an `.htaccess` for security headers (example below).

### Recommended `.htaccess`
```apache
<IfModule mod_headers.c>
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-Content-Type-Options "nosniff"
  Header set Referrer-Policy "strict-origin-when-cross-origin"
  Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
  Header set X-XSS-Protection "0"
  Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains" env=HTTPS
</IfModule>
FileETag None
<IfModule mod_deflate.c>
 AddOutputFilterByType DEFLATE text/css application/javascript text/javascript text/html text/plain image/svg+xml
</IfModule>
```

## Customization
Color system lives in `assets/css/styles.css` under `:root`:
```css
:root {
   --clr-accent: #b646ff;      /* violet */
   --clr-accent-2: #ff3fb3;    /* magenta */
   --clr-accent-3: #ff8f5a;    /* peach */
   --grad-accent: linear-gradient(135deg,var(--clr-accent),var(--clr-accent-2) 46%, var(--clr-accent-3) 78%);
   --ff-display: 'Orbitron','Montserrat','Poppins',system-ui,sans-serif;
}
```
Quick theme tweaks:
- Shift hue: change `--clr-accent` & `--clr-accent-2` then regenerate gradient stops to keep smooth transition.
- Reduce intensity: lower shadow alpha values in `--shadow-glow` and card hover radial gradients.
- Accessibility high‑contrast mode: add a class (e.g. `body.high-contrast`) that swaps `--clr-text-muted` to a lighter tone and removes ambient overlays.

### Font System
Display headings now use a techno geometric stack for the neon aesthetic:
```
```
Fallback retains legibility and weight consistency. To swap for another futuristic face (e.g. Exo, Audiowide):
1. Add new family to Google Fonts link in all page `<head>` blocks.
2. Replace `Orbitron` in `--ff-display`.
3. Verify letter-spacing does not compromise readability (adjust `.section-title` letter-spacing if needed).

### Hero Imagery Control (Simplified)
Global background usage is now centralized:
```css
:root { --hero-img: url('assets/img/background.jpeg'); }
```
All hero sections and category imagery consume this single variable.

Adjustments you can still make:
- Brightness / saturation: tweak `--hero-bg-filter` in `:root`.
- Overlay removal: keep using `.hero--plain` (current) or remove that class and restore gradient overlays if desired.
- Page-specific image (optional): add an inline style or page level `<style>` overriding `--hero-img` for that page only.

To revert to unique images per section, reintroduce individual hero background rules or custom variables (e.g. `--services-img`). For now everything intentionally stays unified.

## Accessibility & SEO
- Semantic headings and landmarks used.
- Gradient text uses `background-clip: text`; ensure fallback color sets if disabling gradients (optional future enhancement).
- Focus outline color updated (`:focus-visible`) to `--clr-accent-2` for high distinguishability against dark backgrounds.
- Checked core contrast (body text vs backgrounds ≥ 4.5:1, small badge text ~3.8:1; consider solid background variant for badges if targeting strict AA for <14px).
- Provide prefers-reduced-motion handling already present; animations minimal and reversible.
- Add Open Graph tags & real contact info before production:
```html
<meta property="og:title" content="Premier Family Salon & Hair Spa" />
<meta property="og:description" content="Luxury hair, skin, makeup & hair spa rituals." />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://yourdomain.com/" />
<meta property="og:image" content="https://yourdomain.com/assets/img/og-cover.jpg" />
```

## Security / Hardening
- Consider adding a captcha (e.g. hCaptcha or Cloudflare Turnstile) to `contact.html`.
- Log server-side requests (append to a file) for traceability.
- Rate-limit via simple session / IP timestamp if you see abuse.
- Ensure hosting email sending quota is not exceeded.

## Future Enhancements
- Product detail modal + filterable categories (JS).
- Switch to AJAX form submit with JSON response.
- Add analytics (respect privacy laws / consent).
- Add minified CSS/JS via a build pipeline if performance tuning is needed.

## License for Images
All external Pexels image references have been removed. The single local file `assets/img/background.jpeg` should be replaced with properly licensed brand imagery before production if it is still a placeholder.

## Contact
Add your real salon contact details and verify all business info before launch.

---
Happy launching! Reach out if you’d like a version with a CMS or booking integration.
