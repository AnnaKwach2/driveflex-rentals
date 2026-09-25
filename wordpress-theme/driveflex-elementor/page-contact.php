<?php
get_header();
if ( have_posts() ) : the_post();
	if ( driveflex_elementor_page() ) {
		the_content();
	} else {
		?>
		<section class="df-contact-hero"><div class="df-container"><span class="df-eyebrow">GET IN TOUCH</span><h1>Contact &amp; Support</h1><p>Planning a rental or need help choosing a vehicle? Tell us about your journey and our team will help with availability, pricing and collection arrangements.</p></div></section>
		<section class="df-container df-contact-layout"><div class="df-contact-form-card"><span class="df-eyebrow">SEND A MESSAGE</span><h2>How can we help?</h2><form class="df-contact-form"><div class="df-contact-fields"><label>First name<input name="firstName" autocomplete="given-name" required></label><label>Last name<input name="lastName" autocomplete="family-name" required></label><label class="df-wide">Email address<input name="email" type="email" autocomplete="email" required></label><label class="df-wide">Phone number<input name="phone" type="tel" autocomplete="tel" required></label><label class="df-wide">Message<textarea name="message" rows="6" required></textarea></label></div><p class="df-form-error" role="alert"></p><button class="df-button" type="submit">Send message on WhatsApp →</button></form></div><aside class="df-contact-details"><article><b>☎</b><div><h3>Phone &amp; WhatsApp</h3><a href="tel:+254706449960">+254 706 449960</a><a href="https://wa.me/254706449960" target="_blank" rel="noopener">Start a WhatsApp chat →</a></div></article><article><b>⌖</b><div><h3>Service area</h3><p>Nairobi, Kenya</p><p>Airport pickup and delivery arrangements available.</p></div></article><article><b>◷</b><div><h3>Booking support</h3><p>Availability, rental estimates, chauffeur services and long-term hire.</p></div></article></aside></section>
		<section class="df-container df-contact-map"><iframe title="DriveFlex Rentals service location in Nairobi" src="https://www.google.com/maps?q=Nairobi%2C%20Kenya&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></section>
		<section class="df-contact-faq"><div class="df-container"><span class="df-eyebrow">COMMON QUESTIONS</span><h2>Frequently Asked Questions</h2><details open><summary>What information do I need when requesting a car?</summary><p>Provide your preferred vehicle, pickup and return dates, pickup location, phone number and trip details. ID and payment information are requested only after availability is confirmed.</p></details><details><summary>Can DriveFlex arrange airport pickup?</summary><p>Yes. Pickup and vehicle delivery can be arranged for JKIA and Wilson Airport, subject to confirmation.</p></details><details><summary>Do you offer chauffeur services?</summary><p>Chauffeur-driven options can be requested for business travel, airport transfers, events and longer journeys.</p></details></div></section>
		<a class="df-whatsapp" href="https://wa.me/254706449960" target="_blank" rel="noopener">WhatsApp us</a>
		<?php
	}
endif;
get_footer();
