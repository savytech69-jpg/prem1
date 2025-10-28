# Premier Family Salon & Hair Spa Website

A modern, multi-page static website (HTML/CSS/JS + minimal PHP mail handler) suitable for deployment on typical shared hosting / cPanel environments.

## Pages
- `index.html` – Landing page with improved high‑quality hero image, featured services, testimonials placeholder, CTA.
- `services.html` – Structured service categories + NEW interactive image tile mosaic linking to Hair, Skin, Makeup, Hand & Feet anchors.
- `products.html` – Sample product gallery with placeholder images, categories & prices (grid cards upgraded with image + price tag).
- `contact.html` – Booking / inquiry form with honeypot anti-spam.
- `booking.php` – Server-side handler that validates and emails booking requests.

Assets in `assets/css`, `assets/js`, and `assets/img` (logo images now referenced directly in the header). Add / replace favicon & hero imagery as needed.

### Recent UI Enhancements
1. Replaced text-based brand mark with raster logo (`assets/img/logo-dark.png`).
2. Upgraded home hero background for richer ambiance & subtle zoom animation.
3. Added responsive service category tiles (`.service-tiles` / `.tile`) with hover zoom and accessible labels.
4. Expanded product cards: now include image area, category, concise description (class `.desc`) and price pill (`.price-tag`).
5. Tweaked mobile nav panel width (opens from right, 70% viewport width) and refined small‑screen hero spacing.
6. Added minor accessibility improvements (descriptive alt for logo, aria labels for tile links, preserved heading hierarchy).

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
- Replace stock royalty‑free hero / tile / product images (currently Pexels) with brand photography.
- Provide a vector `logo.svg` and swap `<img>` sources for sharper scaling (current PNG retained as fallback).
- Populate real testimonials in `index.html`.
- Update pricing, add more cards or restructure service groups.
- For products: extend data via JSON and hydrate grid dynamically (future enhancement).

## Accessibility & SEO
- Semantic headings and landmarks used.
- Gradient text uses `background-clip: text` fallback.
- Add Open Graph tags & real contact info before production:
```html
<meta property="og:title" content="Premier Family Salon & Hair Spa" />
<meta property="og:description" content="Luxury hair, skin, makeup & spa rituals." />
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
All placeholder background images referenced are from Pexels (royalty free). Replace with your own licensed media prior to production.

## Contact
Add your real salon contact details and verify all business info before launch.

---
Happy launching! Reach out if you’d like a version with a CMS or booking integration.
