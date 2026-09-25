<?php
get_header();
if ( have_posts() ) : the_post();
	if ( driveflex_elementor_page() || trim( get_the_content() ) ) {
		the_content();
	} else {
		$fleet_url = home_url( '/fleet/' );
		$image = static fn( string $name ): string => get_theme_file_uri( 'assets/images/' . $name );
		?>
		<section class="df-hero">
			<div class="df-container df-hero-copy"><h1>Drive Your Journey,<br><span>Your Way.</span></h1><p>Explore Kenya with comfort and style.<br>Your perfect rental. Your next adventure.</p><div class="df-perks"><div class="df-perk"><b>◇</b><span><strong>Clear Daily Rates</strong><small>Prices in Kenyan shillings</small></span></div><div class="df-perk"><b>♧</b><span><strong>Flexible Rentals</strong><small>For your kind of journey</small></span></div><div class="df-perk"><b>◷</b><span><strong>Easy Trip Planning</strong><small>Find your perfect ride</small></span></div></div></div>
			<form class="df-search" action="<?php echo esc_url( $fleet_url ); ?>"><label>Pick-up location<select name="location"><option>Nairobi City Centre</option><option>Jomo Kenyatta Airport</option><option>Wilson Airport</option><option>Mombasa</option></select></label><label>Pick-up date<input type="date" name="pickup" required></label><label>Drop-off date<input type="date" name="dropoff" required></label><label>Pick-up time<input type="time" name="time" value="10:00" required></label><button class="df-button" type="submit">Search cars</button></form>
		</section>
		<section class="df-section df-reveal"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow">CHOOSE YOUR RIDE</span><h2>Find the perfect car</h2></div><a class="df-button" href="<?php echo esc_url( $fleet_url ); ?>">View all cars →</a></div><div class="df-category-grid">
		<?php foreach ( array( array( 'Economy Cars', 'vitz.webp', 'KSh 3,500' ), array( 'Sedans', 'axio.webp', 'KSh 4,000' ), array( 'SUVs', '2021tx.webp', 'KSh 12,000' ), array( 'Luxury Cars', 'merc-c200.webp', 'KSh 15,000' ), array( 'Vans', 'hiace.webp', 'KSh 15,000' ) ) as $category ) : ?>
			<a class="df-category-card" href="<?php echo esc_url( $fleet_url ); ?>"><img src="<?php echo esc_url( $image( $category[1] ) ); ?>" alt="<?php echo esc_attr( $category[0] ); ?>" loading="lazy"><h3><?php echo esc_html( $category[0] ); ?></h3><p>Compare seats, doors and luggage</p><span>From <strong class="df-price"><?php echo esc_html( $category[2] ); ?></strong> / day</span></a>
		<?php endforeach; ?>
		</div></div></section>
		<section class="df-section df-reveal"><div class="df-container df-promos"><article class="df-promo"><img src="<?php echo esc_url( $image( 'yellow-convertible.webp' ) ); ?>" alt="Toyota Prado TX"><div class="df-promo-copy"><small>Weekend Special</small><h2>Get <span>20% OFF</span></h2><p>A little more freedom for your next escape.</p><a class="df-button" href="<?php echo esc_url( $fleet_url ); ?>">Book now</a></div></article><article class="df-promo"><img src="<?php echo esc_url( $image( 'luxury-driver.webp' ) ); ?>" alt="Toyota Alphard with chauffeur"><div class="df-promo-copy"><small>Long Term Rentals</small><h2>Save up to <span>30%</span></h2><p>Settle in for business trips and extended stays of 30+ days.</p><a class="df-button" href="<?php echo esc_url( $fleet_url ); ?>">Learn more</a></div></article></div></section>
		<section class="df-section df-reveal" id="how-it-works"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow">SIMPLE BOOKING</span><h2>How it works</h2></div></div><div class="df-steps"><?php foreach ( array( array( '⌕', 'Search', 'Choose your location, dates and car.' ), array( '▦', 'Request', 'Review the estimate and send your details.' ), array( '♧', 'Confirm', 'We confirm availability before payment.' ), array( '⚿', 'Pick up', 'Collect your car at the agreed location.' ), array( '↶', 'Return', 'Return it at the agreed time.' ) ) as $step ) : ?><article class="df-step"><span><?php echo esc_html( $step[0] ); ?></span><div><h3><?php echo esc_html( $step[1] ); ?></h3><p><?php echo esc_html( $step[2] ); ?></p></div></article><?php endforeach; ?></div></div></section>
		<?php
	}
endif;
get_footer();
