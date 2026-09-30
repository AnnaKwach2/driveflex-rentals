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
	const cleanError = value => {
		const box = document.createElement('div');
		box.innerHTML = String(value || '');
		return (box.textContent || '').replace(/\s+/g, ' ').trim() || 'Something went wrong. Please try again.';
	};

  async function api(path, options = {}) {
    const response = await fetch(`${DriveFlexBooking.api}${path}`, {
      ...options,
      headers: { 'Content-Type': 'application/json', ...(options.headers || {}) }
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(cleanError(data.message));
    return data;
  }

  function card(vehicle) {
    return `<article class="df-card">
	  <small class="df-card-category">${esc(vehicle.category)}</small>
      <div class="df-card-image">${vehicle.image ? `<img src="${esc(vehicle.image)}" alt="${esc(vehicle.name)}" loading="lazy" decoding="async">` : ''}</div>
	  <div class="df-card-body"><h3>${esc(vehicle.name)} <small>or similar</small></h3>
      <p class="df-price">${money(vehicle.rate)} <span>/ day</span></p>
	  <div class="df-specs"><span><b>⚙</b><small>TRANS</small><strong>${esc(vehicle.transmission)}</strong></span><span><b>♙</b><small>SEATS</small><strong>${vehicle.seats}</strong></span><span><b>▣</b><small>LUGGAGE</small><strong>${vehicle.luggage}</strong></span><span><b>▱</b><small>DOORS</small><strong>${vehicle.doors}</strong></span></div>
	  <div class="df-card-actions"><button class="df-outline" data-view="${vehicle.id}">View Car</button><button class="df-button df-dark" data-book="${vehicle.id}" ${vehicle.active ? '' : 'disabled'}>${vehicle.active ? 'Reserve ›' : 'Unavailable'}</button></div></div>
    </article>`;
  }

  function renderFleet() {
    const categories = ['All', ...new Set(state.vehicles.map(vehicle => vehicle.category))];
	root.innerHTML = `<section class="df-shell"><header class="df-fleet-hero"><div><small>OUR COLLECTION</small><h1>Unrivaled Performance.</h1><p>ⓘ &nbsp; Self-drive rentals require a minimum booking period of 3 days.</p></div><span>${esc(DriveFlexBooking.currency)}</span></header><div class="df-fleet-tools"><label>Category<select id="df-category">${categories.map(category => `<option value="${esc(category)}">${category === 'All' ? 'All vehicles' : esc(category)}</option>`).join('')}</select></label><p class="df-status" id="df-status" aria-live="polite"></p><label>Sort<select id="df-sort"><option value="recommended">Recommended</option><option value="price-low">Price: Low to High</option><option value="price-high">Price: High to Low</option></select></label></div><div class="df-grid" id="df-grid"></div><p class="df-fleet-note">Daily rates are shown in Kenyan shillings. Vehicle availability and booking requirements are confirmed for your selected dates.</p></section><dialog class="df-dialog" id="df-dialog"><button class="df-close" aria-label="Close">×</button><div id="df-dialog-content"></div></dialog>`;
    const grid = document.getElementById('df-grid');
	const draw = () => {
	  const category = document.getElementById('df-category').value;
	  const sort = document.getElementById('df-sort').value;
	  const vehicles = state.vehicles.filter(vehicle => category === 'All' || vehicle.category === category);
	  if (sort === 'price-low') vehicles.sort((a, b) => a.rate - b.rate);
	  if (sort === 'price-high') vehicles.sort((a, b) => b.rate - a.rate);
      grid.innerHTML = vehicles.map(card).join('');
      document.getElementById('df-status').textContent = `${vehicles.length} vehicles · prices in Kenyan shillings`;
    };
	document.getElementById('df-category').addEventListener('change', draw);
	document.getElementById('df-sort').addEventListener('change', draw);
	draw();
  }

	function viewStep(vehicle) {
	  openDialog(`<div class="df-dialog-body df-vehicle-view">${vehicle.image ? `<img src="${esc(vehicle.image)}" alt="${esc(vehicle.name)}">` : ''}<div><small>${esc(vehicle.category)}</small><h2>${esc(vehicle.name)}</h2><p>${esc(vehicle.description)}</p><p class="df-price">${money(vehicle.rate)} <span>/ day</span></p><div class="df-specs"><span><b>⚙</b><small>TRANS</small><strong>${esc(vehicle.transmission)}</strong></span><span><b>♙</b><small>SEATS</small><strong>${vehicle.seats}</strong></span><span><b>▣</b><small>LUGGAGE</small><strong>${vehicle.luggage}</strong></span><span><b>▱</b><small>DOORS</small><strong>${vehicle.doors}</strong></span></div><button class="df-button df-dark" data-book="${vehicle.id}">Reserve this vehicle ›</button></div></div>`);
	}

  function openDialog(content) {
    const dialog = document.getElementById('df-dialog');
    document.getElementById('df-dialog-content').innerHTML = content;
    if (!dialog.open) dialog.showModal();
    dialog.querySelector('.df-close').focus();
  }

  function tripStep(vehicle) {
    state.vehicle = vehicle;
	openDialog(`<div class="df-dialog-body"><div class="df-progress"><b>1. Trip & price</b><span>2. Your details</span></div><small>PLAN YOUR RENTAL</small><h2>${esc(vehicle.name)}</h2><p>${money(vehicle.rate)} per day · choose your service type</p>
	  <form id="df-estimate-form"><fieldset class="df-service-selector"><legend>Service Type</legend><label><input type="radio" name="service_type" value="self_drive" required><span>Self Drive</span></label><label><input type="radio" name="service_type" value="with_driver" required><span>With Driver</span></label></fieldset><div class="df-service-notice" id="df-service-notice">Select a service type to see its minimum booking period.</div><div class="df-fields">
      <label>Pick-up date<input name="pickup_date" type="date" min="${today}" value="${addDays(today, 1)}" required></label>
	  <label>Drop-off date<input name="dropoff_date" type="date" min="${addDays(today, 2)}" value="${addDays(today, 2)}" required></label>
      <label>Pick-up time<input name="pickup_time" type="time" value="10:00" required></label>
      <label>Drop-off time<input name="dropoff_time" type="time" value="10:00" required></label>
      <label>Pick-up location<select name="pickup_location">${locations()}</select></label>
      <label>Drop-off location<select name="dropoff_location">${locations()}</select></label></div>
      <p class="df-error" id="df-error" role="alert"></p><div id="df-estimate-result"></div><div class="df-actions"><button class="df-button" type="submit">Calculate estimate →</button><button class="df-button df-dark" id="df-continue" type="button" hidden>Continue to booking →</button></div></form></div>`);
	const form = document.getElementById('df-estimate-form');
	const pickup = form.elements.pickup_date;
	const dropoff = form.elements.dropoff_date;
	const notice = document.getElementById('df-service-notice');
	const syncMinimum = () => {
	  const selected = form.querySelector('[name="service_type"]:checked')?.value;
	  const days = selected === 'self_drive' ? vehicle.minimum_days : 1;
	  const earliest = addDays(pickup.value || today, days);
	  dropoff.min = earliest;
	  if (!dropoff.value || dropoff.value < earliest) dropoff.value = earliest;
	  notice.innerHTML = selected === 'self_drive'
	    ? `<strong>${days}-Day Minimum</strong><span>Self-drive bookings require a minimum rental period of ${days} days.</span>`
	    : selected === 'with_driver'
	      ? '<strong>With Driver</strong><span>Bookings with a driver can be requested for one day or longer. Driver service charges are confirmed by DriveFlex Rentals.</span>'
	      : 'Select a service type to see its minimum booking period.';
	};
	form.querySelectorAll('[name="service_type"]').forEach(input => input.addEventListener('change', syncMinimum));
	pickup.addEventListener('change', syncMinimum);
	form.addEventListener('submit', calculate);
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
	  document.getElementById('df-estimate-result').innerHTML = `<div class="df-estimate"><div><strong>${state.estimate.days} days × ${money(state.estimate.daily_rate)}</strong>${state.estimate.discount ? `<small>${state.estimate.discount_percent}% long-term saving (−${money(state.estimate.discount)})</small>` : ''}<small>Estimated vehicle rental total</small>${state.trip.service_type === 'with_driver' ? '<small>Driver service charges will be confirmed by DriveFlex Rentals.</small>' : ''}</div><b>${money(state.estimate.total)}</b></div>${state.estimate.available ? '' : '<p class="df-warning">These dates currently overlap a reserved booking. You can still send a request for alternative availability.</p>'}`;
      const next = document.getElementById('df-continue');
      next.hidden = false;
      next.onclick = detailsStep;
    } catch (err) {
      error.textContent = err.message;
      document.getElementById('df-continue').hidden = true;
    }
  }

  function detailsStep() {
	const serviceLabel = state.trip.service_type === 'self_drive' ? 'Self Drive' : 'With Driver';
    openDialog(`<div class="df-dialog-body"><div class="df-progress"><span>1. Trip & price</span><b>2. Your details</b></div><small>COMPLETE YOUR REQUEST</small><h2>Your booking details</h2>
	  <div class="df-summary">${state.vehicle.image ? `<img src="${esc(state.vehicle.image)}" alt="">` : ''}<div><strong>${esc(state.vehicle.name)}</strong><span>${esc(serviceLabel)}</span><span>${esc(state.trip.pickup_date)} at ${esc(state.trip.pickup_time)} → ${esc(state.trip.dropoff_date)} at ${esc(state.trip.dropoff_time)}</span><span>${esc(state.trip.pickup_location)} → ${esc(state.trip.dropoff_location)}</span><b>${money(state.estimate.total)} estimated vehicle rental total</b></div></div>
      <form id="df-booking-form"><div class="df-fields">
	  <label>Full Name<input name="name" autocomplete="name" required></label><label>Phone Number<input name="phone" type="tel" autocomplete="tel" placeholder="e.g. +254 700 000 000" required></label>
	  <label>Email Address<input name="email" type="email" autocomplete="email" required></label><label>ID / Passport Number (optional)<input type="text" placeholder="Required later when paying for the vehicle" disabled></label><label class="df-wide">Intended Area of Use<input name="intended_area" type="text" placeholder="e.g. Nairobi, Naivasha and Nakuru" required></label><label class="df-wide">Trip details / request notes<textarea name="notes" rows="4" placeholder="Tell us about your trip, delivery request or other requirements" required></textarea></label></div>
	  <div class="df-organization"><label class="df-consent"><input id="df-organization-toggle" name="organization_booking" type="checkbox" value="1"> I am making this booking on behalf of my organization</label><label id="df-organization-name" hidden>Organization Name<input name="organization_name" type="text" placeholder="Enter organization name"></label></div>
	  <div class="df-discount"><label>Discount Code <small>(optional)</small><input name="discount_code" type="text" placeholder="ENTER CODE"></label></div>
	  <div class="df-agreements"><label class="df-consent"><input name="terms" type="checkbox" value="1" required> <span>I acknowledge that I have read and agree to the <a href="${esc(DriveFlexBooking.termsUrl)}" target="_blank" rel="noopener">Terms of Service</a> and <a href="${esc(DriveFlexBooking.privacyUrl)}" target="_blank" rel="noopener">Privacy Policy</a> set by DriveFlex Rentals.</span></label>${state.trip.service_type === 'self_drive' ? '<label class="df-consent"><input name="license_confirmed" type="checkbox" value="1" required> <span>I confirm that I have held a valid driver’s licence for 3 years or longer.</span></label>' : ''}</div>
	  <p class="df-error" id="df-error" role="alert"></p><div class="df-actions"><button class="df-outline" id="df-back" type="button">← Back</button><button class="df-button" type="submit">Prepare booking request →</button></div>
	  <p class="df-note">Your vehicle is confirmed after the ${esc(DriveFlexBooking.company)} team verifies availability and contacts you. ID/passport and payment are requested later.</p></form></div>`);
    document.getElementById('df-back').onclick = () => tripStep(state.vehicle);
	const organizationToggle = document.getElementById('df-organization-toggle');
	const organizationName = document.getElementById('df-organization-name');
	organizationToggle.addEventListener('change', () => {
	  organizationName.hidden = !organizationToggle.checked;
	  organizationName.querySelector('input').required = organizationToggle.checked;
	});
    document.getElementById('df-booking-form').addEventListener('submit', submitBooking);
  }

  async function submitBooking(event) {
    event.preventDefault();
    const button = event.currentTarget.querySelector('[type=submit]');
    const fields = Object.fromEntries(new FormData(event.currentTarget).entries());
    button.disabled = true;
    try {
	  const result = await api('/bookings', { method: 'POST', body: JSON.stringify({ ...state.trip, ...fields, vehicle_id: state.vehicle.id }) });
      openDialog(`<div class="df-confirmation"><span>✓</span><small>REQUEST RECEIVED</small><h2>Thank you</h2><p>Your reference is <strong>${esc(result.reference)}</strong>. ${esc(DriveFlexBooking.company)} will contact you after checking availability.</p><div class="df-estimate"><strong>${esc(state.vehicle.name)} · ${state.estimate.days} days</strong><b>${money(state.estimate.total)}</b></div>${result.whatsapp_url ? `<a class="df-button" href="${esc(result.whatsapp_url)}" target="_blank" rel="noopener">Continue on WhatsApp →</a>` : ''}</div>`);
    } catch (err) {
      document.getElementById('df-error').textContent = err.message;
      button.disabled = false;
    }
  }

  root.addEventListener('click', event => {
    const trigger = event.target.closest('[data-book]');
    if (trigger) tripStep(state.vehicles.find(vehicle => String(vehicle.id) === trigger.dataset.book));
	const view = event.target.closest('[data-view]');
	if (view) viewStep(state.vehicles.find(vehicle => String(vehicle.id) === view.dataset.view));
    if (event.target.matches('.df-close')) document.getElementById('df-dialog').close();
  });

  api('/vehicles').then(vehicles => { state.vehicles = vehicles; renderFleet(); }).catch(error => { root.innerHTML = `<p class="df-error">${esc(error.message)}</p>`; });
})();
