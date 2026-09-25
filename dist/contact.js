'use strict';
document.getElementById('contact-form')?.addEventListener('submit', event => {
  event.preventDefault();
  const form = new FormData(event.currentTarget);
  const firstName = String(form.get('firstName') || '').trim();
  const lastName = String(form.get('lastName') || '').trim();
  const email = String(form.get('email') || '').trim();
  const phone = String(form.get('phone') || '').trim();
  const message = String(form.get('message') || '').trim();
  const error = document.getElementById('contact-error');
  if (!firstName || !lastName || !email || !phone || !message) {
    error.textContent = 'Please complete all fields before continuing.';
    return;
  }
  error.textContent = '';
  const text = ['Hello DriveFlex Rentals,', '', message, '', `Name: ${firstName} ${lastName}`, `Phone: ${phone}`, `Email: ${email}`].join('\n');
  window.open(`https://wa.me/254706449960?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
});
