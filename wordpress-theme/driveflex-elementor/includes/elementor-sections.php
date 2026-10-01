<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

final class DriveFlex_Elementor_Section extends Widget_Base {
	private string $section;
	public function __construct( string $section, array $data = array(), ?array $args = null ) { $this->section = $section; parent::__construct( $data, $args ); }
	public function get_name(): string { return 'driveflex-' . $this->section; }
	public function get_title(): string { return 'DriveFlex ' . ucwords( str_replace( '-', ' ', $this->section ) ); }
	public function get_icon(): string { return 'eicon-section'; }
	public function get_categories(): array { return array( 'general' ); }
	public function get_style_depends(): array { return array( 'driveflex-theme' ); }
	private function text( string $id, string $label, string $default, string $type = Controls_Manager::TEXT ): void { $this->add_control( $id, array( 'label' => $label, 'type' => $type, 'default' => $default ) ); }
	private function image( string $id, string $label, string $file ): void { $this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => get_theme_file_uri( 'assets/images/' . $file ) ) ) ); }
	private function fleet_link(): void { $this->add_control( 'fleet_url', array( 'label' => 'Fleet page URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => home_url( '/fleet/' ) ) ) ); }

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => 'Content' ) );
		switch ( $this->section ) {
			case 'hero':
				$this->text( 'title', 'Heading', 'Drive Your Journey,' ); $this->text( 'accent', 'Orange heading', 'Your Way.' ); $this->text( 'intro', 'Introduction', "Explore Kenya with comfort and style.\nYour perfect rental. Your next adventure.", Controls_Manager::TEXTAREA ); $this->image( 'background', 'Background image', 'driveflex-nairobi-hero.webp' ); $this->fleet_link();
				break;
			case 'categories':
				$this->text( 'eyebrow', 'Small heading', 'CHOOSE YOUR RIDE' ); $this->text( 'title', 'Heading', 'Find the perfect car' ); $this->fleet_link();
				break;
			case 'offers':
				$this->text( 'first_title', 'First offer heading', 'Get 20% OFF' ); $this->text( 'first_text', 'First offer text', 'A little more freedom for your next escape.', Controls_Manager::TEXTAREA ); $this->image( 'first_image', 'First offer image', 'yellow-convertible.webp' ); $this->text( 'second_title', 'Second offer heading', 'Save up to 30%' ); $this->text( 'second_text', 'Second offer text', 'Settle in for business trips and extended stays of 30+ days.', Controls_Manager::TEXTAREA ); $this->image( 'second_image', 'Second offer image', 'luxury-driver.webp' ); $this->fleet_link();
				break;
			case 'deals':
				$this->text( 'title', 'Heading', 'Top Deals' ); $this->fleet_link();
				break;
			case 'steps':
				$this->text( 'eyebrow', 'Small heading', 'SIMPLE BOOKING' ); $this->text( 'title', 'Heading', 'How it works' );
				break;
			case 'benefits':
				$this->text( 'title', 'Heading', 'Why Drive With Us' ); $this->text( 'callout_title', 'Callout heading', 'Your next ride. On the go.' ); $this->text( 'callout_text', 'Callout text', 'Browse, compare and plan your rental from any device.', Controls_Manager::TEXTAREA ); $this->image( 'callout_image', 'Callout image', 'driveflex-hero.webp' ); $this->fleet_link();
				break;
			case 'contact-hero':
				$this->text( 'eyebrow', 'Small heading', 'GET IN TOUCH' ); $this->text( 'title', 'Heading', 'Contact & Support' ); $this->text( 'intro', 'Introduction', 'Planning a rental or need help choosing a vehicle? Tell us about your journey and our team will help with availability, pricing and collection arrangements.', Controls_Manager::TEXTAREA );
				break;
			case 'contact-main':
				$this->text( 'title', 'Form heading', 'How can we help?' ); $this->text( 'phone', 'Phone', '+254 706 449960' ); $this->text( 'whatsapp', 'WhatsApp URL', 'https://wa.me/254706449960' ); $this->text( 'location', 'Location', 'Nairobi, Kenya' );
				break;
			case 'contact-faq':
				$this->text( 'eyebrow', 'Small heading', 'COMMON QUESTIONS' ); $this->text( 'title', 'Heading', 'Frequently Asked Questions' );
				break;
		}
		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display(); $fleet = $s['fleet_url']['url'] ?? home_url( '/fleet/' ); $asset = static fn( string $file ): string => get_theme_file_uri( 'assets/images/' . $file );
		switch ( $this->section ) {
			case 'hero': ?>
				<section class="df-hero" id="locations" style="background-image:url('<?php echo esc_url( $s['background']['url'] ); ?>')"><div class="df-container df-hero-copy"><h1><?php echo esc_html( $s['title'] ); ?><br><span><?php echo esc_html( $s['accent'] ); ?></span></h1><p><?php echo nl2br( esc_html( $s['intro'] ) ); ?></p><div class="df-perks"><div class="df-perk"><b>◇</b><span><strong>Clear Daily Rates</strong><small>Prices in Kenyan shillings</small></span></div><div class="df-perk"><b>♧</b><span><strong>Flexible Rentals</strong><small>For your kind of journey</small></span></div><div class="df-perk"><b>◷</b><span><strong>Easy Trip Planning</strong><small>Find your perfect ride</small></span></div></div></div><div class="df-search-panel"><div class="df-search-tabs"><button type="button" class="is-active">♧ Rent a Car</button><button type="button">↗ One Way</button><button type="button">▦ Long Term</button></div><form class="df-search" action="<?php echo esc_url( $fleet ); ?>"><label>Pick-up Location<select name="location"><option>Nairobi City Centre</option><option>Jomo Kenyatta Airport</option><option>Wilson Airport</option><option>Mombasa</option></select></label><label>Pick-up Date<input type="date" name="pickup" required></label><label>Pick-up Time<input type="time" name="pickup_time" value="10:00"></label><label>Drop-off Date<input type="date" name="dropoff" required></label><label>Drop-off Time<input type="time" name="dropoff_time" value="10:00"></label><button class="df-button" type="submit">Search Cars</button></form></div></section><?php break;
			case 'categories': ?>
				<section class="df-section"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h2><?php echo esc_html( $s['title'] ); ?></h2></div><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">View all cars →</a></div><div class="df-category-grid"><?php foreach ( array( array( 'Economy Cars', 'vitz.webp', 'KSh 3,500' ), array( 'Sedans', 'axio.webp', 'KSh 4,000' ), array( 'SUVs', '2021tx.webp', 'KSh 12,000' ), array( 'Luxury Cars', 'merc-c200.webp', 'KSh 15,000' ), array( 'Vans', 'hiace.webp', 'KSh 15,000' ) ) as $c ) : ?><a class="df-category-card" href="<?php echo esc_url( $fleet ); ?>"><img src="<?php echo esc_url( $asset( $c[1] ) ); ?>" alt="<?php echo esc_attr( $c[0] ); ?>"><h3><?php echo esc_html( $c[0] ); ?></h3><p>Compare seats, doors and luggage</p><span>From <strong class="df-price"><?php echo esc_html( $c[2] ); ?></strong> / day</span></a><?php endforeach; ?></div></div></section><?php break;
			case 'offers': ?>
				<section class="df-section" id="deals"><div class="df-container df-promos"><article class="df-promo"><img src="<?php echo esc_url( $s['first_image']['url'] ); ?>" alt="Rental offer"><div class="df-promo-copy"><small>Weekend Special</small><h2><?php echo esc_html( $s['first_title'] ); ?></h2><p><?php echo esc_html( $s['first_text'] ); ?></p><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">Book now</a></div></article><article class="df-promo"><img src="<?php echo esc_url( $s['second_image']['url'] ); ?>" alt="Long-term rental offer"><div class="df-promo-copy"><small>Long Term Rentals</small><h2><?php echo esc_html( $s['second_title'] ); ?></h2><p><?php echo esc_html( $s['second_text'] ); ?></p><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">Learn more</a></div></article></div></section><?php break;
			case 'deals': ?>
				<section class="df-section df-deals-section"><div class="df-container"><div class="df-section-heading"><h2><?php echo esc_html( $s['title'] ); ?></h2><a class="df-outline-button" href="<?php echo esc_url( $fleet ); ?>">View all deals →</a></div><div class="df-deal-grid"><?php foreach ( array( array( 'Toyota Vitz', 'vitz.webp', 'KSh 3,500' ), array( 'Toyota Axio', 'axio.webp', 'KSh 4,000' ), array( 'Toyota Prado TX', '2021tx.webp', 'KSh 12,000' ), array( 'Mercedes C-200', 'merc-c200.webp', 'KSh 15,000' ), array( 'Toyota Harrier', 'harrier.webp', 'KSh 8,000' ) ) as $d ) : ?><a class="df-deal-card" href="<?php echo esc_url( $fleet ); ?>"><img src="<?php echo esc_url( $asset( $d[1] ) ); ?>" alt="<?php echo esc_attr( $d[0] ); ?>"><h3><?php echo esc_html( $d[0] ); ?></h3><strong><?php echo esc_html( $d[2] ); ?></strong><small> / day</small></a><?php endforeach; ?></div></div></section><?php break;
			case 'steps': ?>
				<section class="df-section" id="how-it-works"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h2><?php echo esc_html( $s['title'] ); ?></h2></div></div><div class="df-steps"><?php foreach ( array( array( '⌕', 'Search', 'Choose your location, dates and car.' ), array( '▦', 'Request', 'Review the estimate and send your details.' ), array( '♧', 'Confirm', 'We confirm availability before payment.' ), array( '⚿', 'Pick up', 'Collect your car at the agreed location.' ), array( '↶', 'Return', 'Return it at the agreed time.' ) ) as $step ) : ?><article class="df-step"><span><?php echo esc_html( $step[0] ); ?></span><div><h3><?php echo esc_html( $step[1] ); ?></h3><p><?php echo esc_html( $step[2] ); ?></p></div></article><?php endforeach; ?></div></div></section><?php break;
			case 'benefits': ?>
				<section class="df-section"><div class="df-container df-benefits-layout"><div><div class="df-section-heading"><h2><?php echo esc_html( $s['title'] ); ?></h2></div><div class="df-benefit-grid"><?php foreach ( array( array( '01', 'Flexible travel', 'Choose a rental period that fits your plans.' ), array( '02', 'Kenya-wide journeys', 'Vehicles for city travel, airport pickups and road trips.' ), array( '03', 'Clear daily rates', 'Compare prices and specifications before you book.' ) ) as $b ) : ?><article class="df-benefit"><span><?php echo esc_html( $b[0] ); ?></span><p><?php echo esc_html( $b[2] ); ?></p><strong><?php echo esc_html( $b[1] ); ?></strong></article><?php endforeach; ?></div></div><aside class="df-mobile-callout"><img src="<?php echo esc_url( $s['callout_image']['url'] ); ?>" alt="DriveFlex rental car"><div><h2><?php echo esc_html( $s['callout_title'] ); ?></h2><p><?php echo esc_html( $s['callout_text'] ); ?></p><a class="df-outline-button" href="<?php echo esc_url( $fleet ); ?>">Explore on mobile →</a></div></aside></div></section><?php break;
			case 'contact-hero': ?>
				<section class="df-contact-hero"><div class="df-container"><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h1><?php echo esc_html( $s['title'] ); ?></h1><p><?php echo esc_html( $s['intro'] ); ?></p></div></section><?php break;
			case 'contact-main': $wa = $s['whatsapp']; ?>
				<section class="df-container df-contact-layout"><div class="df-contact-form-card"><span class="df-eyebrow">SEND A MESSAGE</span><h2><?php echo esc_html( $s['title'] ); ?></h2><form class="df-contact-form" data-whatsapp="<?php echo esc_url( $wa ); ?>"><div class="df-contact-fields"><label>First name<input name="firstName" required></label><label>Last name<input name="lastName" required></label><label class="df-wide">Email address<input name="email" type="email" required></label><label class="df-wide">Phone number<input name="phone" type="tel" required></label><label class="df-wide">Message<textarea name="message" rows="6" required></textarea></label></div><p class="df-form-error"></p><button class="df-button" type="submit">Send message on WhatsApp →</button></form></div><aside class="df-contact-details"><article><b>☎</b><div><h3>Phone &amp; WhatsApp</h3><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $s['phone'] ) ); ?>"><?php echo esc_html( $s['phone'] ); ?></a><a href="<?php echo esc_url( $wa ); ?>">Start a WhatsApp chat →</a></div></article><article><b>⌖</b><div><h3>Service area</h3><p><?php echo esc_html( $s['location'] ); ?></p></div></article></aside></section><section class="df-container df-contact-map"><iframe title="DriveFlex location" src="https://www.google.com/maps?q=<?php echo rawurlencode( $s['location'] ); ?>&amp;output=embed" loading="lazy"></iframe></section><?php break;
			case 'contact-faq': ?>
				<section class="df-contact-faq"><div class="df-container"><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h2><?php echo esc_html( $s['title'] ); ?></h2><details open><summary>What information do I need when requesting a car?</summary><p>Provide your preferred vehicle, dates, location, phone number and trip details.</p></details><details><summary>Can DriveFlex arrange airport pickup?</summary><p>Yes. Airport pickup and delivery can be arranged subject to confirmation.</p></details><details><summary>Do you offer chauffeur services?</summary><p>Chauffeur-driven options can be requested for business travel, transfers, events and longer journeys.</p></details></div></section><?php break;
		}
	}
}
