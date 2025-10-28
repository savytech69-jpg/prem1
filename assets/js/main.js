// Premier Family Salon & Hair Spa - Global JS
// Handles: mobile navigation, scroll reveal animations, form validation (client side)

(function() {
  const qs = (sel, ctx=document) => ctx.querySelector(sel);
  const qsa = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

  // Mobile Nav Toggle
  const toggleBtn = qs('.mobile-toggle');
  const nav = qs('.nav-links');
  if (toggleBtn && nav) {
    toggleBtn.addEventListener('click', () => {
      nav.classList.toggle('open');
      const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
      toggleBtn.setAttribute('aria-expanded', String(!expanded));
    });
    // Close when clicking a link on mobile
    nav.addEventListener('click', e => {
      if (e.target.matches('a')) nav.classList.remove('open');
    });
  }

  // Scroll Reveal
  const animated = qsa('[data-animate]');
  if ('IntersectionObserver' in window && animated.length) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.15 });
    animated.forEach(el => io.observe(el));
  } else {
    // Fallback
    animated.forEach(el => el.classList.add('in'));
  }

  // Client-side Form Validation (Contact / Booking Form)
  const form = qs('form[data-booking]');
  if (form) {
    const fields = {
      name: form.querySelector('[name=name]'),
      phone: form.querySelector('[name=phone]'),
      email: form.querySelector('[name=email]'),
      service: form.querySelector('[name=service]'),
      date: form.querySelector('[name=appointment_date]'),
      time: form.querySelector('[name=appointment_time]'),
      message: form.querySelector('[name=message]')
    };

    const setError = (input, msg) => {
      const wrap = input.closest('.form-field');
      if (!wrap) return;
      let err = wrap.querySelector('.error-msg');
      if (!err) {
        err = document.createElement('div');
        err.className = 'error-msg';
        wrap.appendChild(err);
      }
      err.textContent = msg;
      err.style.display = msg ? 'block' : 'none';
    };

    const clearErrors = () => qsa('.error-msg', form).forEach(e => { e.textContent=''; e.style.display='none'; });

    const validators = {
      name: v => v.trim().length >= 2 || 'Please enter your full name',
      phone: v => /[0-9\-+() ]{7,}/.test(v) || 'Enter a valid phone number',
      email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Enter a valid email',
      service: v => v.trim() !== '' || 'Select a service',
      appointment_date: v => v.trim() !== '' || 'Choose a date',
      appointment_time: v => v.trim() !== '' || 'Choose a time'
    };

    form.addEventListener('submit', e => {
      clearErrors();
      let hasError = false;
      Object.entries(validators).forEach(([field, fn]) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;
        const result = fn(input.value || '');
        if (result !== true) {
          hasError = true;
          setError(input, result);
        }
      });
      // Honeypot
      const honeypot = form.querySelector('[name=company]');
      if (honeypot && honeypot.value) {
        // Bot – silently stop
        e.preventDefault();
        return false;
      }
      if (hasError) {
        e.preventDefault();
      }
    });
  }

  // Parallax Effect on Hero Background
  const hero = qs('.hero');
  const heroBg = qs('.hero-bg');
  if (hero && heroBg) {
    let ticking = false;
    hero.addEventListener('mousemove', e => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          const rect = hero.getBoundingClientRect();
          const x = ((e.clientX - rect.left) / rect.width - 0.5) * 20; // max ±10px
          const y = ((e.clientY - rect.top) / rect.height - 0.5) * 20;
          heroBg.style.transform = `scale(1.08) translate(${x}px, ${y}px)`;
          ticking = false;
        });
        ticking = true;
      }
    });
    
    hero.addEventListener('mouseleave', () => {
      heroBg.style.transform = '';
    });

    // Smooth scroll-based parallax
    let lastScrollY = window.scrollY;
    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          const scrollY = window.scrollY;
          const parallaxOffset = scrollY * 0.3; // 30% speed
          if (heroBg) {
            heroBg.style.transform = `scale(1.08) translateY(${parallaxOffset}px)`;
          }
          lastScrollY = scrollY;
          ticking = false;
        });
        ticking = true;
      }
    };
    
    window.addEventListener('scroll', handleScroll, { passive: true });
  }
})();

