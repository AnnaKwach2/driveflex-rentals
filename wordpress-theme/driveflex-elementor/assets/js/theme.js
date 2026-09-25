(() => {
  'use strict';
  const toggle = document.querySelector('.df-menu-toggle');
  const nav = document.querySelector('.df-navigation');
  if (toggle && nav) toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(open));
  });
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const items = document.querySelectorAll('.df-reveal');
  if (reduce || !('IntersectionObserver' in window)) items.forEach(item => item.classList.add('is-visible'));
  else {
    const observer = new IntersectionObserver(entries => entries.forEach(entry => {
      if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); }
    }), { rootMargin: '0px 0px -8% 0px', threshold: .08 });
    items.forEach(item => observer.observe(item));
  }
  const search = document.querySelector('.df-search');
  if (search) search.addEventListener('submit', event => {
    event.preventDefault();
    const data = new FormData(search);
    location.href = `${search.action}?${new URLSearchParams(data).toString()}`;
  });
  document.querySelector('.df-contact-form')?.addEventListener('submit', event => {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    const fields = Object.fromEntries(data.entries());
    const error = event.currentTarget.querySelector('.df-form-error');
    if (!fields.firstName || !fields.lastName || !fields.email || !fields.phone || !fields.message) {
      error.textContent = 'Please complete all fields before continuing.';
      return;
    }
    error.textContent = '';
    const text = `Hello DriveFlex Rentals,\n\n${fields.message}\n\nName: ${fields.firstName} ${fields.lastName}\nPhone: ${fields.phone}\nEmail: ${fields.email}`;
    window.open(`https://wa.me/254706449960?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
  });
})();
