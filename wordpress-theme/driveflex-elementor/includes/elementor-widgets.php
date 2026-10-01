<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

final class DriveFlex_Home_Elementor_Widget extends Widget_Base {
	public function get_name(): string { return 'driveflex-home-layout'; }
	public function get_title(): string { return 'DriveFlex Home Layout'; }
	public function get_icon(): string { return 'eicon-site-logo'; }
	public function get_categories(): array { return array( 'general' ); }
	public function get_style_depends(): array { return array( 'driveflex-theme' ); }

	protected function register_controls(): void {
		$this->start_controls_section( 'hero', array( 'label' => 'Hero' ) );
		$this->add_control( 'hero_title', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Drive Your Journey,' ) );
		$this->add_control( 'hero_accent', array( 'label' => 'Orange heading', 'type' => Controls_Manager::TEXT, 'default' => 'Your Way.' ) );
		$this->add_control( 'hero_text', array( 'label' => 'Introduction', 'type' => Controls_Manager::TEXTAREA, 'default' => "Explore Kenya with comfort and style.\nYour perfect rental. Your next adventure." ) );
		$this->add_control( 'hero_image', array( 'label' => 'Background image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => get_theme_file_uri( 'assets/images/driveflex-nairobi-hero.webp' ) ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'content', array( 'label' => 'Section headings and offers' ) );
		foreach ( array(
			'ride_heading' => array( 'Choose Your Ride heading', 'Find the perfect car' ),
			'weekend_title' => array( 'Weekend offer heading', 'Get 20% OFF' ),
			'weekend_text' => array( 'Weekend offer text', 'A little more freedom for your next escape.' ),
			'long_title' => array( 'Long-term heading', 'Save up to 30%' ),
			'long_text' => array( 'Long-term text', 'Settle in for business trips and extended stays of 30+ days.' ),
			'deals_heading' => array( 'Deals heading', 'Top Deals' ),
			'steps_heading' => array( 'Steps heading', 'How it works' ),
			'benefits_heading' => array( 'Benefits heading', 'Why Drive With Us' ),
		) as $id => $field ) {
			$this->add_control( $id, array( 'label' => $field[0], 'type' => str_contains( $id, 'text' ) ? Controls_Manager::TEXTAREA : Controls_Manager::TEXT, 'default' => $field[1] ) );
		}
		$this->add_control( 'weekend_image', array( 'label' => 'Weekend offer image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => get_theme_file_uri( 'assets/images/yellow-convertible.webp' ) ) ) );
		$this->add_control( 'long_image', array( 'label' => 'Long-term offer image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => get_theme_file_uri( 'assets/images/luxury-driver.webp' ) ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'links', array( 'label' => 'Links' ) );
		$this->add_control( 'fleet_url', array( 'label' => 'Fleet page URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => home_url( '/fleet/' ) ) ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		$fleet = ! empty( $s['fleet_url']['url'] ) ? $s['fleet_url']['url'] : home_url( '/fleet/' );
		$image = static fn( string $name ): string => get_theme_file_uri( 'assets/images/' . $name );
		$hero = ! empty( $s['hero_image']['url'] ) ? $s['hero_image']['url'] : $image( 'driveflex-nairobi-hero.webp' );
		?>
		<section class="df-hero df-elementor-home" id="locations" style="background-image:url('<?php echo esc_url( $hero ); ?>')"><div class="df-container df-hero-copy"><h1><?php echo esc_html( $s['hero_title'] ); ?><br><span><?php echo esc_html( $s['hero_accent'] ); ?></span></h1><p><?php echo nl2br( esc_html( $s['hero_text'] ) ); ?></p><div class="df-perks"><div class="df-perk"><b>◇</b><span><strong>Clear Daily Rates</strong><small>Prices in Kenyan shillings</small></span></div><div class="df-perk"><b>♧</b><span><strong>Flexible Rentals</strong><small>For your kind of journey</small></span></div><div class="df-perk"><b>◷</b><span><strong>Easy Trip Planning</strong><small>Find your perfect ride</small></span></div></div></div><div class="df-search-panel"><div class="df-search-tabs"><button type="button" class="is-active">♧ Rent a Car</button><button type="button">↗ One Way</button><button type="button">▦ Long Term</button></div><form class="df-search" action="<?php echo esc_url( $fleet ); ?>"><label>Pick-up Location<select name="location"><option>Nairobi City Centre</option><option>Jomo Kenyatta Airport</option><option>Wilson Airport</option><option>Mombasa</option></select></label><label>Pick-up Date<input type="date" name="pickup" required></label><label>Pick-up Time<input type="time" name="pickup_time" value="10:00" required></label><label>Drop-off Date<input type="date" name="dropoff" required></label><label>Drop-off Time<input type="time" name="dropoff_time" value="10:00" required></label><button class="df-button" type="submit">Search Cars</button></form></div></section>
		<section class="df-section"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow">CHOOSE YOUR RIDE</span><h2><?php echo esc_html( $s['ride_heading'] ); ?></h2></div><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">View all cars →</a></div><div class="df-category-grid"><?php foreach ( array( array( 'Economy Cars', 'vitz.webp', 'KSh 3,500' ), array( 'Sedans', 'axio.webp', 'KSh 4,000' ), array( 'SUVs', '2021tx.webp', 'KSh 12,000' ), array( 'Luxury Cars', 'merc-c200.webp', 'KSh 15,000' ), array( 'Vans', 'hiace.webp', 'KSh 15,000' ) ) as $c ) : ?><a class="df-category-card" href="<?php echo esc_url( $fleet ); ?>"><img src="<?php echo esc_url( $image( $c[1] ) ); ?>" alt="<?php echo esc_attr( $c[0] ); ?>" loading="lazy"><h3><?php echo esc_html( $c[0] ); ?></h3><p>Compare seats, doors and luggage</p><span>From <strong class="df-price"><?php echo esc_html( $c[2] ); ?></strong> / day</span></a><?php endforeach; ?></div></div></section>
		<section class="df-section" id="deals"><div class="df-container df-promos"><article class="df-promo"><img src="<?php echo esc_url( $s['weekend_image']['url'] ); ?>" alt="Weekend rental offer"><div class="df-promo-copy"><small>Weekend Special</small><h2><?php echo esc_html( $s['weekend_title'] ); ?></h2><p><?php echo esc_html( $s['weekend_text'] ); ?></p><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">Book now</a></div></article><article class="df-promo"><img src="<?php echo esc_url( $s['long_image']['url'] ); ?>" alt="Long-term rental offer"><div class="df-promo-copy"><small>Long Term Rentals</small><h2><?php echo esc_html( $s['long_title'] ); ?></h2><p><?php echo esc_html( $s['long_text'] ); ?></p><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">Learn more</a></div></article></div></section>
		<section class="df-section df-deals-section"><div class="df-container"><div class="df-section-heading"><h2><?php echo esc_html( $s['deals_heading'] ); ?></h2><a class="df-outline-button" href="<?php echo esc_url( $fleet ); ?>">View all deals →</a></div><div class="df-deal-grid"><?php foreach ( array( array( 'Toyota Vitz', 'vitz.webp', 'KSh 3,500' ), array( 'Toyota Axio', 'axio.webp', 'KSh 4,000' ), array( 'Toyota Prado TX', '2021tx.webp', 'KSh 12,000' ), array( 'Mercedes C-200', 'merc-c200.webp', 'KSh 15,000' ), array( 'Toyota Harrier', 'harrier.webp', 'KSh 8,000' ) ) as $d ) : ?><a class="df-deal-card" href="<?php echo esc_url( $fleet ); ?>"><img src="<?php echo esc_url( $image( $d[1] ) ); ?>" alt="<?php echo esc_attr( $d[0] ); ?>" loading="lazy"><h3><?php echo esc_html( $d[0] ); ?></h3><strong><?php echo esc_html( $d[2] ); ?></strong><small> / day</small></a><?php endforeach; ?></div><p class="df-section-caption">Choose your dates to view the rental total for your journey.</p></div></section>
		<section class="df-section" id="how-it-works"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow">SIMPLE BOOKING</span><h2><?php echo esc_html( $s['steps_heading'] ); ?></h2></div></div><div class="df-steps"><?php foreach ( array( array( '⌕', 'Search', 'Choose your location, dates and car.' ), array( '▦', 'Request', 'Review the estimate and send your details.' ), array( '♧', 'Confirm', 'We confirm availability before payment.' ), array( '⚿', 'Pick up', 'Collect your car at the agreed location.' ), array( '↶', 'Return', 'Return it at the agreed time.' ) ) as $step ) : ?><article class="df-step"><span><?php echo esc_html( $step[0] ); ?></span><div><h3><?php echo esc_html( $step[1] ); ?></h3><p><?php echo esc_html( $step[2] ); ?></p></div></article><?php endforeach; ?></div></div></section>
		<section class="df-section"><div class="df-container df-benefits-layout"><div><div class="df-section-heading"><h2><?php echo esc_html( $s['benefits_heading'] ); ?></h2></div><div class="df-benefit-grid"><?php foreach ( array( array( '01', 'Flexible travel', 'Choose a rental period that fits your plans.' ), array( '02', 'Kenya-wide journeys', 'Vehicles for city travel, airport pickups and road trips.' ), array( '03', 'Clear daily rates', 'Compare prices and specifications before you book.' ) ) as $b ) : ?><article class="df-benefit"><span><?php echo esc_html( $b[0] ); ?></span><p><?php echo esc_html( $b[2] ); ?></p><strong><?php echo esc_html( $b[1] ); ?></strong></article><?php endforeach; ?></div></div><aside class="df-mobile-callout"><img src="<?php echo esc_url( $image( 'driveflex-hero.webp' ) ); ?>" alt="DriveFlex rental car" loading="lazy"><div><h2>Your next ride.<br>On the go.</h2><p>Browse, compare and plan your rental from any device.</p><a class="df-outline-button" href="<?php echo esc_url( $fleet ); ?>">Explore on mobile →</a></div></aside></div></section>
		<?php
	}
}

final class DriveFlex_Contact_Elementor_Widget extends Widget_Base {
	public function get_name(): string { return 'driveflex-contact-layout'; }
	public function get_title(): string { return 'DriveFlex Contact Layout'; }
	public function get_icon(): string { return 'eicon-mail'; }
	public function get_categories(): array { return array( 'general' ); }
	public function get_style_depends(): array { return array( 'driveflex-theme' ); }
	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => 'Contact content' ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Contact & Support' ) );
		$this->add_control( 'intro', array( 'label' => 'Introduction', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Planning a rental or need help choosing a vehicle? Tell us about your journey and our team will help with availability, pricing and collection arrangements.' ) );
		$this->add_control( 'phone', array( 'label' => 'Phone', 'type' => Controls_Manager::TEXT, 'default' => '+254 706 449960' ) );
		$this->add_control( 'whatsapp', array( 'label' => 'WhatsApp URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'https://wa.me/254706449960' ) ) );
		$this->add_control( 'location', array( 'label' => 'Location', 'type' => Controls_Manager::TEXT, 'default' => 'Nairobi, Kenya' ) );
		$this->end_controls_section();
	}
	protected function render(): void {
		$s = $this->get_settings_for_display(); $wa = $s['whatsapp']['url'] ?: 'https://wa.me/254706449960';
		?>
		<section class="df-contact-hero"><div class="df-container"><span class="df-eyebrow">GET IN TOUCH</span><h1><?php echo esc_html( $s['heading'] ); ?></h1><p><?php echo esc_html( $s['intro'] ); ?></p></div></section><section class="df-container df-contact-layout"><div class="df-contact-form-card"><span class="df-eyebrow">SEND A MESSAGE</span><h2>How can we help?</h2><form class="df-contact-form" data-whatsapp="<?php echo esc_url( $wa ); ?>"><div class="df-contact-fields"><label>First name<input name="firstName" required></label><label>Last name<input name="lastName" required></label><label class="df-wide">Email address<input name="email" type="email" required></label><label class="df-wide">Phone number<input name="phone" type="tel" required></label><label class="df-wide">Message<textarea name="message" rows="6" required></textarea></label></div><p class="df-form-error" role="alert"></p><button class="df-button" type="submit">Send message on WhatsApp →</button></form></div><aside class="df-contact-details"><article><b>☎</b><div><h3>Phone &amp; WhatsApp</h3><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $s['phone'] ) ); ?>"><?php echo esc_html( $s['phone'] ); ?></a><a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener">Start a WhatsApp chat →</a></div></article><article><b>⌖</b><div><h3>Service area</h3><p><?php echo esc_html( $s['location'] ); ?></p><p>Airport pickup and delivery arrangements available.</p></div></article><article><b>◷</b><div><h3>Booking support</h3><p>Availability, rental estimates, chauffeur services and long-term hire.</p></div></article></aside></section><section class="df-container df-contact-map"><iframe title="DriveFlex Rentals service location" src="https://www.google.com/maps?q=<?php echo rawurlencode( $s['location'] ); ?>&amp;output=embed" loading="lazy"></iframe></section><section class="df-contact-faq"><div class="df-container"><span class="df-eyebrow">COMMON QUESTIONS</span><h2>Frequently Asked Questions</h2><details open><summary>What information do I need when requesting a car?</summary><p>Provide your preferred vehicle, pickup and return dates, pickup location, phone number and trip details.</p></details><details><summary>Can DriveFlex arrange airport pickup?</summary><p>Yes. Airport pickup and delivery can be arranged subject to confirmation.</p></details><details><summary>Do you offer chauffeur services?</summary><p>Chauffeur-driven options can be requested for business travel, transfers, events and longer journeys.</p></details></div></section><a class="df-whatsapp" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
		<?php
	}
}
