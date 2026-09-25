(() => {
  'use strict';
  const root = document.getElementById('driveflex-booking-app');
  if (!root || !window.DriveFlexBooking) return;

  const state = { vehicles: [], vehicle: null, trip: null, estimate: null };
  const esc = value => String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
  const money = value => `${DriveFlexBooking.currency} ${Number(value).toLocaleString('en-KE', { maximumFractionDigits: 0 })}`;
  const today = DriveFlexBooking.today;
  const addDays = (date, days) => { const next = new Date(`${date}T12:00:00`); next.setDate(next.getDate() + days); return next.toISOString().slice(0, 10); };
  const locations = () => DriveFlexBooking.locations.map(location => `<option>${esc(location)}</option>`).join('');

  async function api(path, options = {}) {
    const response = await fetch(`${DriveFlexBooking.api}${path}`, {
      ...options,
      headers: { 'Content-Type': 'application/json', ...(options.headers || {}) }
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(data.message || 'Something went wrong. Please try again.');
    return data;
  }

  function card(vehicle) {
    return `<article class="df-card">
      <div class="df-card-image">${vehicle.image ? `<img src="${esc(vehicle.image)}" alt="${esc(vehicle.name)}" loading="lazy" decoding="async">` : ''}</div>
      <div class="df-card-body"><small>${esc(vehicle.category)}</small><h3>${esc(vehicle.name)}</h3>
      <p class="df-price">${money(vehicle.rate)} <span>/ day</span></p>
      <div class="df-specs"><span>${esc(vehicle.transmission)}</span><span>${vehicle.seats} seats</span><span>${vehicle.luggage} luggage</span><span>${vehicle.doors} doors</span></div>
      <button class="df-button" data-book="${vehicle.id}" ${vehicle.active ? '' : 'disabled'}>${vehicle.active ? 'Reserve →' : 'Unavailable'}</button></div>
    </article>`;
  }

  function renderFleet() {
    const categories = ['All', ...new Set(state.vehicles.map(vehicle => vehicle.category))];
    root.innerHTML = `<section class="df-shell"><div class="df-heading"><div><small>OUR COLLECTION</small><h2>Choose your DriveFlex vehicle</h2></div><label>Category<select id="df-category">${categories.map(category => `<option>${esc(category)}</option>`).join('')}</select></label></div><p class="df-status" id="df-status" aria-live="polite"></p><div class="df-grid" id="df-grid"></div></section><dialog class="df-dialog" id="df-dialog"><button class="df-close" aria-label="Close">×</button><div id="df-dialog-content"></div></dialog>`;
    const grid = document.getElementById('df-grid');
    const draw = category => {
      const vehicles = state.vehicles.filter(vehicle => category === 'All' || vehicle.category === category);
      grid.innerHTML = vehicles.map(card).join('');
      document.getElementById('df-status').textContent = `${vehicles.length} vehicles · prices in Kenyan shillings`;
    };
    document.getElementById('df-category').addEventListener('change', event => draw(event.target.value));
    draw('All');
  }

  function openDialog(content) {
    const dialog = document.getElementById('df-dialog');
    document.getElementById('df-dialog-content').innerHTML = content;
    if (!dialog.open) dialog.showModal();
    dialog.querySelector('.df-close').focus();
  }

  function tripStep(vehicle) {
    state.vehicle = vehicle;
    openDialog(`<div class="df-dialog-body"><div class="df-progress"><b>1. Trip & price</b><span>2. Your details</span></div><small>PLAN YOUR RENTAL</small><h2>${esc(vehicle.name)}</h2><p>${money(vehicle.rate)} per day · ${vehicle.minimum_days}-day minimum</p>
      <form id="df-estimate-form"><div class="df-fields">
      <label>Pick-up date<input name="pickup_date" type="date" min="${today}" value="${today}" required></label>
      <label>Drop-off date<input name="dropoff_date" type="date" min="${addDays(today, vehicle.minimum_days)}" value="${addDays(today, vehicle.minimum_days)}" required></label>
      <label>Pick-up time<input name="pickup_time" type="time" value="10:00" required></label>
      <label>Drop-off time<input name="dropoff_time" type="time" value="10:00" required></label>
      <label>Pick-up location<select name="pickup_location">${locations()}</select></label>
      <label>Drop-off location<select name="dropoff_location">${locations()}</select></label></div>
      <p class="df-error" id="df-error" role="alert"></p><div id="df-estimate-result"></div><div class="df-actions"><button class="df-button" type="submit">Calculate estimate →</button><button class="df-button df-dark" id="df-continue" type="button" hidden>Continue to booking →</button></div></form></div>`);
    document.getElementById('df-estimate-form').addEventListener('submit', calculate);
  }

  async function calculate(event) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    state.trip = Object.fromEntries(form.entries());
    state.trip.vehicle_id = state.vehicle.id;
    const error = document.getElementById('df-error');
    try {
      state.estimate = await api('/estimate', { method: 'POST', body: JSON.stringify(state.trip) });
      error.textContent = '';
      document.getElementById('df-estimate-result').innerHTML = `<div class="df-estimate"><div><strong>${state.estimate.days} days × ${money(state.estimate.daily_rate)}</strong>${state.estimate.discount ? `<small>${state.estimate.discount_percent}% long-term saving (−${money(state.estimate.discount)})</small>` : ''}<small>Estimated rental total</small></div><b>${money(state.estimate.total)}</b></div>${state.estimate.available ? '' : '<p class="df-warning">These dates currently overlap a reserved booking. You can still send a request for alternative availability.</p>'}`;
      const next = document.getElementById('df-continue');
      next.hidden = false;
      next.onclick = detailsStep;
    } catch (err) {
      error.textContent = err.message;
      document.getElementById('df-continue').hidden = true;
    }
  }

  function detailsStep() {
    openDialog(`<div class="df-dialog-body"><div class="df-progress"><span>1. Trip & price</span><b>2. Your details</b></div><small>COMPLETE YOUR REQUEST</small><h2>Your booking details</h2>
      <div class="df-summary">${state.vehicle.image ? `<img src="${esc(state.vehicle.image)}" alt="">` : ''}<div><strong>${esc(state.vehicle.name)}</strong><span>${esc(state.trip.pickup_date)} at ${esc(state.trip.pickup_time)} → ${esc(state.trip.dropoff_date)} at ${esc(state.trip.dropoff_time)}</span><span>${esc(state.trip.pickup_location)} → ${esc(state.trip.dropoff_location)}</span><b>${money(state.estimate.total)} estimated total</b></div></div>
      <form id="df-booking-form"><div class="df-fields">
      <label>Full name<input name="name" autocomplete="name" required></label><label>Phone number<input name="phone" type="tel" autocomplete="tel" required></label>
      <label>Email address<input name="email" type="email" autocomplete="email" required></label><label class="df-wide">Trip details / request notes<textarea name="notes" rows="4" required></textarea></label></div>
      <label class="df-consent"><input name="terms" type="checkbox" value="1" required> I confirm these trip details and agree to the rental terms.</label>
      <p class="df-error" id="df-error" role="alert"></p><div class="df-actions"><button class="df-outline" id="df-back" type="button">← Back</button><button class="df-button" type="submit">Send booking request →</button></div>
      <p class="df-note">ID/passport and payment are requested only after DriveFlex confirms vehicle availability.</p></form></div>`);
    document.getElementById('df-back').onclick = () => tripStep(state.vehicle);
    document.getElementById('df-booking-form').addEventListener('submit', submitBooking);
  }

  async function submitBooking(event) {
    event.preventDefault();
    const button = event.currentTarget.querySelector('[type=submit]');
    const fields = Object.fromEntries(new FormData(event.currentTarget).entries());
    button.disabled = true;
    try {
      const result = await api('/bookings', { method: 'POST', body: JSON.stringify({ ...state.trip, ...fields, vehicle_id: state.vehicle.id, terms: true }) });
      openDialog(`<div class="df-confirmation"><span>✓</span><small>REQUEST RECEIVED</small><h2>Thank you</h2><p>Your reference is <strong>${esc(result.reference)}</strong>. DriveFlex will contact you after checking availability.</p><div class="df-estimate"><strong>${esc(state.vehicle.name)} · ${state.estimate.days} days</strong><b>${money(state.estimate.total)}</b></div>${result.whatsapp_url ? `<a class="df-button" href="${esc(result.whatsapp_url)}" target="_blank" rel="noopener">Continue on WhatsApp →</a>` : ''}</div>`);
    } catch (err) {
      document.getElementById('df-error').textContent = err.message;
      button.disabled = false;
    }
  }

  root.addEventListener('click', event => {
    const trigger = event.target.closest('[data-book]');
    if (trigger) tripStep(state.vehicles.find(vehicle => String(vehicle.id) === trigger.dataset.book));
    if (event.target.matches('.df-close')) document.getElementById('df-dialog').close();
  });

  api('/vehicles').then(vehicles => { state.vehicles = vehicles; renderFleet(); }).catch(error => { root.innerHTML = `<p class="df-error">${esc(error.message)}</p>`; });
})();
