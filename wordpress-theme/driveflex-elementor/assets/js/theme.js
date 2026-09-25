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
})();
