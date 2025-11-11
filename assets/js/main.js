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

  // Enhanced Form Handling with AJAX and Success Messages
  const handleFormSubmit = (form, formType) => {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.textContent : '';

    const setError = (input, msg) => {
      const wrap = input.closest('.form-group') || input.closest('.form-field');
      if (!wrap) return;
      let err = wrap.querySelector('.error-msg');
      if (!err) {
        err = document.createElement('div');
        err.className = 'error-msg';
        err.style.color = '#ff3fb3';
        err.style.fontSize = '0.85rem';
        err.style.marginTop = '0.5rem';
        wrap.appendChild(err);
      }
      err.textContent = msg;
      err.style.display = msg ? 'block' : 'none';
      if (msg) input.style.borderColor = '#ff3fb3';
    };

    const clearErrors = () => {
      qsa('.error-msg', form).forEach(e => { e.textContent=''; e.style.display='none'; });
      qsa('input, select, textarea', form).forEach(input => input.style.borderColor = '');
    };

    const showSuccessMessage = (message) => {
      // Create success modal
      const modal = document.createElement('div');
      modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease;
      `;

      modal.innerHTML = `
        <div style="
          background: linear-gradient(185deg, #16161f, #0f0f13);
          border: 2px solid #b646ff;
          border-radius: 16px;
          padding: 3rem 2rem;
          max-width: 500px;
          text-align: center;
          animation: slideUp 0.4s ease;
          box-shadow: 0 20px 60px rgba(182, 70, 255, 0.3);
        ">
          <div style="
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #b646ff, #ff3fb3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
          ">✓</div>
          <h2 style="
            font-family: 'Montserrat', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(90deg, #b646ff, #ff3fb3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
          ">Success!</h2>
          <p style="
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2rem;
          ">${message}</p>
          <button onclick="this.closest('div[style*=fixed]').remove()" style="
            background: linear-gradient(135deg, #b646ff, #ff3fb3);
            color: white;
            border: none;
            padding: 0.875rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: transform 0.2s;
          " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            Close
          </button>
        </div>
      `;

      // Add animations
      const style = document.createElement('style');
      style.textContent = `
        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }
        @keyframes slideUp {
          from { transform: translateY(30px); opacity: 0; }
          to { transform: translateY(0); opacity: 1; }
        }
      `;
      document.head.appendChild(style);

      document.body.appendChild(modal);

      // Auto close after 5 seconds
      setTimeout(() => modal.remove(), 5000);
    };

    const showErrorMessage = (message) => {
      const errorDiv = document.createElement('div');
      errorDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ff3fb3;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(255, 63, 179, 0.4);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
      `;
      errorDiv.innerHTML = `
        <strong style="display: block; margin-bottom: 0.5rem;">Error</strong>
        ${message}
      `;

      document.body.appendChild(errorDiv);
      setTimeout(() => errorDiv.remove(), 4000);
    };

    const validators = {
      name: v => v.trim().length >= 2 || 'Please enter your full name (minimum 2 characters)',
      phone: v => {
        const cleaned = v.replace(/[^0-9+]/g, '');
        return /^(\+91)?[6-9]\d{9}$/.test(cleaned) || 'Please enter a valid Indian phone number';
      },
      email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Please enter a valid email address',
      service: v => v.trim() !== '' || 'Please select a service',
      appointment_date: v => v.trim() !== '' || 'Please choose an appointment date',
      program: v => v.trim() !== '' || 'Please select a program'
    };

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors();

      // Honeypot check
      const honeypot = form.querySelector('[name=company]');
      if (honeypot && honeypot.value) {
        return false;
      }

      // Validate required fields
      let hasError = false;
      const requiredFields = qsa('[required]', form);
      
      requiredFields.forEach(input => {
        const fieldName = input.getAttribute('name');
        const validator = validators[fieldName];
        
        if (validator) {
          const result = validator(input.value || '');
          if (result !== true) {
            hasError = true;
            setError(input, result);
          }
        } else if (!input.value.trim()) {
          hasError = true;
          setError(input, 'This field is required');
        }
      });

      if (hasError) return;

      // Show loading state
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        submitBtn.style.opacity = '0.7';
      }

      try {
        const formData = new FormData(form);
        
        const response = await fetch('booking.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
          showSuccessMessage(result.message);
          form.reset();
        } else {
          showErrorMessage(result.message || 'An error occurred. Please try again.');
        }
      } catch (error) {
        console.error('Form submission error:', error);
        showErrorMessage('Failed to submit form. Please check your connection and try again.');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
          submitBtn.style.opacity = '1';
        }
      }
    });
  };

  // Initialize booking form
  const bookingForm = qs('form[data-booking]');
  if (bookingForm) {
    handleFormSubmit(bookingForm, 'booking');
  }

  // Initialize career form
  const careerForm = qs('form.career-form');
  if (careerForm) {
    handleFormSubmit(careerForm, 'career');
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

