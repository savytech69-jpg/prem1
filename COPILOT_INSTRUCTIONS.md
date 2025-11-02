# Copilot Project Instructions – Premier Family Salon & Hair Spa Website

Purpose: Provide AI assistants concise, high-signal guidance for extending and maintaining this static multi-page site (HTML/CSS/JS + minimal PHP).

## 1. Project Overview
- Stack: Pure static HTML/CSS/JS (no build tooling) + `booking.php` mail handler.
- Pages: `index.html`, `services.html`, `products.html`, `contact.html`, plus assets in `assets/`.
- Design System: Dark neon aesthetic (violet / magenta / peach). Centralized CSS variables in `assets/css/styles.css` (`:root`).
- Deployment Target: Shared hosting / cPanel (Apache + PHP 7+). No Node required.

## 2. Architectural Principles
- Keep pages lightweight and semantic; reuse header/footer structure (future improvement: optional PHP includes).
- Global styling uses CSS custom properties; do not hardcode colors – use variables (e.g., `var(--clr-accent-2)`).
- Single hero/background asset via `--hero-img` variable; if introducing per-page hero images, override variable locally rather than changing markup.
- JS kept framework-free; prefer vanilla patterns and progressive enhancement.

## 3. HTML Guidelines
- Maintain accessible landmarks: `header`, `nav`, `main`, `footer`.
- Provide one `<h1>` per page; subsections begin at `<h2>`.
- Use `article` for independent repeatable content (testimonials, product cards, service items) when adding new items.
- Add `aria-current="page"` on active navigation links (if modifying nav).
- Decorative elements must have `aria-hidden="true"`.
- Always supply meaningful `alt` text for images; decorative images can use empty `alt=""`.

## 4. CSS / Styling Conventions
- Extend existing variable set; if new accent is needed, add variable in `:root` instead of inline styles.
- Avoid inline styles except for dynamic background image injection (`style="--img:url('...')"`).
- Reuse utility classes (`.section`, `.badge`, `.btn`, grid systems) rather than duplicating.
- Animations: Respect `prefers-reduced-motion`; wrap new animations with a reduced-motion fallback.
- Performance: Minimize heavy `filter: blur()` or layered gradients for new components.

## 5. JavaScript Conventions (`assets/js/main.js`)
- Use self-invoking function namespace pattern (already present) to limit global scope.
- Helper selectors `qs` / `qsa` should be reused; do not reintroduce jQuery.
- Progressive enhancement: Core functionality must degrade gracefully if JS disabled (forms still submit, content still accessible).
- For new interactive components: add feature detection, keep event listeners passive when scrolling.
- Avoid large libraries; stay < ~10KB additional JS if expanded.

## 6. Form / Booking Enhancements
When extending `booking.php` or form logic:
- Preserve honeypot anti-spam; add rate limiting, length constraints.
- Sanitize all inputs server-side: use `strip_tags`, `htmlspecialchars` when echoing.
- Prefer prepared statements if persistence is added (currently only email send).
- Consider migrating to PHPMailer for SMTP reliability (document config if added).
- Return JSON if `Accept: application/json` header present; keep graceful HTML fallback.

## 7. Accessibility Requirements
- Maintain visible `:focus-visible` outlines; do not remove without replacement.
- Ensure contrast for small text (badges, tags) meets WCAG AA; adjust background or color if adding new variants.
- Provide `aria-live="polite"` region for dynamic form error summaries when implementing AJAX or inline validation.
- Label all form controls with `<label for>` / `id` pairs; placeholders must not serve as the only label.

## 8. Performance & Optimization
- Optimize any new images (target ≤ 150KB for hero, ≤ 80KB for card images; prefer modern formats if host supports – keep fallback to JPEG/PNG if not).
- Lazy load non-critical imagery (`loading="lazy"`).
- Avoid adding additional font families; consider subsetting existing fonts before adding more weights.
- If build tooling is introduced later, produce a minified CSS bundle and critical CSS extraction for above-the-fold hero.

## 9. SEO & Metadata
- Maintain unique `<title>` and `<meta name="description">` per page.
- Add / keep Open Graph and Twitter Card meta when implementing social sharing.
- Provide structured data (JSON-LD LocalBusiness) on `index.html` or `contact.html` if implementing business details.
- Use descriptive anchor text and heading copy; avoid keyword stuffing.

## 10. Security Hardening
- Validate lengths: name ≤ 100 chars, message ≤ 1000 chars, phone ≤ 40.
- Check `appointment_date` & `appointment_time` formats with regex; ensure date not past.
- Implement simple IP-based rate limit (e.g., disallow > 5 submissions / hour) if abuse observed.
- If logging added: rotate log monthly, prevent public access via `.htaccess` (`Deny from all`).
- Avoid exposing server errors to users; use generic messages + internal logging.

## 11. Extensibility Roadmap (Suggested)
- AJAX booking flow (fetch POST → JSON response → inline success panel).
- Product detail modal with accessible dialog pattern (trap focus, ESC closes, `aria-modal="true"`).
- LocalBusiness structured data injection.
- Header/footer partialization (PHP includes) while keeping public-friendly static fallback.
- Asset pipeline (optional) using a lightweight build (e.g. npm scripts + PostCSS) – only if necessary.

## 12. Contribution Guidelines
When proposing changes via Copilot:
1. Read affected files before editing; do not duplicate styles or JS helpers.
2. Keep patches minimal & scoped; avoid unrelated formatting changes.
3. Add comments for non-obvious logic (e.g., anti-spam, rate limit sections).
4. Validate that HTML still passes basic accessibility (landmarks intact, headings sequential).
5. Document new environment variables or configuration in `README.md`.

## 13. Style & Code Quality
- Use 2-space indentation consistently for HTML/CSS/JS/PHP in this project.
- Favor descriptive variable names (e.g., `appointment_date` not `date1`).
- Avoid magic numbers in animations; store new timing values as CSS custom properties if reused.

## 14. Do Not
- Introduce heavy frameworks (React, Vue, etc.) without explicit request.
- Inline large base64 images or fonts (hurts maintainability).
- Remove existing accessibility hooks or `prefers-reduced-motion` checks.
- Hardcode colors bypassing theme variables.

## 15. Example Patterns
CSS Variable Extension:
```css
:root {
  --clr-accent-4: #62ffd6; /* New aqua accent (only if approved) */
}
.btn.aqua { background: linear-gradient(135deg,#62ffd6,#b646ff); }
```

Server-Side Input Length Enforcement (booking.php snippet concept):
```php
function limit($v, $max) { return mb_substr($v, 0, $max); }
$name = limit($name, 100);
$message = limit($message, 1000);
```

Accessible Live Region for Form (HTML addition):
```html
<div id="form-status" class="visually-hidden" aria-live="polite"></div>
```

## 16. Review Checklist Before Commit
- [ ] No deprecated PHP filters used.
- [ ] Form fields have labels + validation messages accessible.
- [ ] No contrast regression (run quick a11y check if colors touched).
- [ ] Meta tags intact; titles unchanged unless intentionally modified.
- [ ] New code uses existing helper patterns.
- [ ] README updated if behavior / config changed.

---
These instructions guide AI and human contributors to keep the site performant, accessible, secure, and consistent with its visual language. Amend this file as architecture evolves.
