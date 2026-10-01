<?php
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

abstract class DriveFlex_Elementor_Section extends Widget_Base {
	protected const SECTION = '';
	private function section(): string { return static::SECTION; }
	public function get_name(): string { return 'driveflex-' . $this->section(); }
	public function get_title(): string { return 'DriveFlex ' . ucwords( str_replace( '-', ' ', $this->section() ) ); }
	public function get_icon(): string { return 'eicon-section'; }
	public function get_categories(): array { return array( 'general' ); }
	public function get_style_depends(): array { return array( 'driveflex-theme' ); }
	private function text( string $id, string $label, string $default, string $type = Controls_Manager::TEXT ): void { $this->add_control( $id, array( 'label' => $label, 'type' => $type, 'default' => $default ) ); }
	private function image( string $id, string $label, string $file ): void { $this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => get_theme_file_uri( 'assets/images/' . $file ) ) ) ); }
	private function fleet_link(): void { $this->add_control( 'fleet_url', array( 'label' => 'Fleet page URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => home_url( '/fleet/' ) ) ) ); }
	private function item_repeater( string $id, string $label, array $defaults, bool $with_image = false, string $marker = 'text' ): void {
		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Title' ) );
		$repeater->add_control( 'text', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => '' ) );
		if ( 'icon' === $marker ) { $repeater->add_control( 'icon', array( 'label' => 'Select icon', 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-circle', 'library' => 'fa-solid' ) ) ); }
		if ( 'text' === $marker ) { $repeater->add_control( 'icon', array( 'label' => 'Number or price', 'type' => Controls_Manager::TEXT, 'default' => '' ) ); }
		if ( $with_image ) { $repeater->add_control( 'image', array( 'label' => 'Upload or select image', 'type' => Controls_Manager::MEDIA ) ); }
		$this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => $defaults, 'title_field' => '{{{ title }}}' ) );
	}
	private function marker( array $item ): void {
		if ( ! empty( $item['image']['url'] ) ) { echo '<img src="' . esc_url( $item['image']['url'] ) . '" alt="">'; return; }
		if ( ! empty( $item['icon'] ) && is_array( $item['icon'] ) ) { Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); return; }
		echo esc_html( (string) ( $item['icon'] ?? '' ) );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => 'Content' ) );
		switch ( $this->section() ) {
			case 'hero':
				$this->text( 'title', 'Heading', 'Drive Your Journey,' ); $this->text( 'accent', 'Orange heading', 'Your Way.' ); $this->text( 'intro', 'Introduction', "Explore Kenya with comfort and style.\nYour perfect rental. Your next adventure.", Controls_Manager::TEXTAREA ); $this->image( 'background', 'Background image', 'driveflex-nairobi-hero.webp' ); $this->item_repeater( 'items', 'Hero benefits', array( array( 'title' => 'Clear Daily Rates', 'text' => 'Prices in Kenyan shillings', 'icon' => array( 'value' => 'fas fa-tags', 'library' => 'fa-solid' ) ), array( 'title' => 'Flexible Rentals', 'text' => 'For your kind of journey', 'icon' => array( 'value' => 'fas fa-calendar-check', 'library' => 'fa-solid' ) ), array( 'title' => 'Easy Trip Planning', 'text' => 'Find your perfect ride', 'icon' => array( 'value' => 'fas fa-route', 'library' => 'fa-solid' ) ) ), true, 'icon' ); $this->fleet_link();
				break;
			case 'categories':
				$this->text( 'eyebrow', 'Small heading', 'CHOOSE YOUR RIDE' ); $this->text( 'title', 'Heading', 'Find the perfect car' ); $this->item_repeater( 'items', 'Vehicle category cards', array( array( 'title' => 'Economy Cars', 'text' => 'Compare seats, doors and luggage', 'icon' => 'KSh 3,500', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/vitz.webp' ) ) ), array( 'title' => 'Sedans', 'text' => 'Compare seats, doors and luggage', 'icon' => 'KSh 4,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/axio.webp' ) ) ), array( 'title' => 'SUVs', 'text' => 'Compare seats, doors and luggage', 'icon' => 'KSh 12,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/2021tx.webp' ) ) ), array( 'title' => 'Luxury Cars', 'text' => 'Compare seats, doors and luggage', 'icon' => 'KSh 15,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/merc-c200.webp' ) ) ), array( 'title' => 'Vans', 'text' => 'Compare seats, doors and luggage', 'icon' => 'KSh 15,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/hiace.webp' ) ) ) ), true ); $this->fleet_link();
				break;
			case 'offers':
				$this->text( 'first_title', 'First offer heading', 'Get 20% OFF' ); $this->text( 'first_text', 'First offer text', 'A little more freedom for your next escape.', Controls_Manager::TEXTAREA ); $this->image( 'first_image', 'First offer image', 'yellow-convertible.webp' ); $this->text( 'second_title', 'Second offer heading', 'Save up to 30%' ); $this->text( 'second_text', 'Second offer text', 'Settle in for business trips and extended stays of 30+ days.', Controls_Manager::TEXTAREA ); $this->image( 'second_image', 'Second offer image', 'luxury-driver.webp' ); $this->fleet_link();
				break;
			case 'deals':
				$this->text( 'title', 'Heading', 'Top Deals' ); $this->item_repeater( 'items', 'Deal cards', array( array( 'title' => 'Toyota Vitz', 'text' => 'Daily rental', 'icon' => 'KSh 3,500', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/vitz.webp' ) ) ), array( 'title' => 'Toyota Axio', 'text' => 'Daily rental', 'icon' => 'KSh 4,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/axio.webp' ) ) ), array( 'title' => 'Toyota Prado TX', 'text' => 'Daily rental', 'icon' => 'KSh 12,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/2021tx.webp' ) ) ), array( 'title' => 'Mercedes C-200', 'text' => 'Daily rental', 'icon' => 'KSh 15,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/merc-c200.webp' ) ) ), array( 'title' => 'Toyota Harrier', 'text' => 'Daily rental', 'icon' => 'KSh 8,000', 'image' => array( 'url' => get_theme_file_uri( 'assets/images/harrier.webp' ) ) ) ), true ); $this->fleet_link();
				break;
			case 'steps':
				$this->text( 'eyebrow', 'Small heading', 'SIMPLE BOOKING' ); $this->text( 'title', 'Heading', 'How it works' ); $this->item_repeater( 'items', 'Booking steps', array( array( 'title' => 'Search', 'text' => 'Choose your location, dates and car.', 'icon' => array( 'value' => 'fas fa-search', 'library' => 'fa-solid' ) ), array( 'title' => 'Request', 'text' => 'Review the estimate and send your details.', 'icon' => array( 'value' => 'fas fa-calendar-alt', 'library' => 'fa-solid' ) ), array( 'title' => 'Confirm', 'text' => 'We confirm availability before payment.', 'icon' => array( 'value' => 'fas fa-check-circle', 'library' => 'fa-solid' ) ), array( 'title' => 'Pick up', 'text' => 'Collect your car at the agreed location.', 'icon' => array( 'value' => 'fas fa-key', 'library' => 'fa-solid' ) ), array( 'title' => 'Return', 'text' => 'Return it at the agreed time.', 'icon' => array( 'value' => 'fas fa-undo-alt', 'library' => 'fa-solid' ) ) ), true, 'icon' );
				break;
			case 'benefits':
				$this->text( 'title', 'Heading', 'Why Drive With Us' ); $this->item_repeater( 'items', 'Benefits', array( array( 'title' => 'Flexible travel', 'text' => 'Choose a rental period that fits your plans.', 'icon' => '01' ), array( 'title' => 'Kenya-wide journeys', 'text' => 'Vehicles for city travel, airport pickups and road trips.', 'icon' => '02' ), array( 'title' => 'Clear daily rates', 'text' => 'Compare prices and specifications before you book.', 'icon' => '03' ) ) ); $this->text( 'callout_title', 'Callout heading', 'Your next ride. On the go.' ); $this->text( 'callout_text', 'Callout text', 'Browse, compare and plan your rental from any device.', Controls_Manager::TEXTAREA ); $this->image( 'callout_image', 'Callout image', 'driveflex-hero.webp' ); $this->fleet_link();
				break;
			case 'contact-hero':
				$this->text( 'eyebrow', 'Small heading', 'GET IN TOUCH' ); $this->text( 'title', 'Heading', 'Contact & Support' ); $this->text( 'intro', 'Introduction', 'Planning a rental or need help choosing a vehicle? Tell us about your journey and our team will help with availability, pricing and collection arrangements.', Controls_Manager::TEXTAREA );
				break;
			case 'contact-main':
				$this->text( 'title', 'Form heading', 'How can we help?' ); $this->text( 'phone', 'Phone', '+254 706 449960' ); $this->text( 'whatsapp', 'WhatsApp URL', 'https://wa.me/254706449960' ); $this->text( 'location', 'Location', 'Nairobi, Kenya' );
				break;
			case 'contact-faq':
				$this->text( 'eyebrow', 'Small heading', 'COMMON QUESTIONS' ); $this->text( 'title', 'Heading', 'Frequently Asked Questions' ); $this->item_repeater( 'items', 'Questions and answers', array( array( 'title' => 'What information do I need when requesting a car?', 'text' => 'Provide your preferred vehicle, dates, location, phone number and trip details.' ), array( 'title' => 'Can DriveFlex arrange airport pickup?', 'text' => 'Yes. Airport pickup and delivery can be arranged subject to confirmation.' ), array( 'title' => 'Do you offer chauffeur services?', 'text' => 'Chauffeur-driven options can be requested for business travel, transfers, events and longer journeys.' ) ), false, 'none' );
				break;
		}
		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display(); $fleet = $s['fleet_url']['url'] ?? home_url( '/fleet/' ); $asset = static fn( string $file ): string => get_theme_file_uri( 'assets/images/' . $file );
		switch ( $this->section() ) {
			case 'hero': ?>
				<section class="df-hero" id="locations" style="background-image:url('<?php echo esc_url( $s['background']['url'] ); ?>')"><div class="df-container df-hero-copy"><h1><?php echo esc_html( $s['title'] ); ?><br><span><?php echo esc_html( $s['accent'] ); ?></span></h1><p><?php echo nl2br( esc_html( $s['intro'] ) ); ?></p><div class="df-perks"><?php foreach ( $s['items'] as $item ) : ?><div class="df-perk"><b><?php $this->marker( $item ); ?></b><span><strong><?php echo esc_html( $item['title'] ); ?></strong><small><?php echo esc_html( $item['text'] ); ?></small></span></div><?php endforeach; ?></div></div><div class="df-search-panel"><div class="df-search-tabs"><button type="button" class="is-active">♧ Rent a Car</button><button type="button">↗ One Way</button><button type="button">▦ Long Term</button></div><form class="df-search" action="<?php echo esc_url( $fleet ); ?>"><label>Pick-up Location<select name="location"><option>Nairobi City Centre</option><option>Jomo Kenyatta Airport</option><option>Wilson Airport</option><option>Mombasa</option></select></label><label>Pick-up Date<input type="date" name="pickup" required></label><label>Pick-up Time<input type="time" name="pickup_time" value="10:00"></label><label>Drop-off Date<input type="date" name="dropoff" required></label><label>Drop-off Time<input type="time" name="dropoff_time" value="10:00"></label><button class="df-button" type="submit">Search Cars</button></form></div></section><?php break;
			case 'categories': ?>
				<section class="df-section"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h2><?php echo esc_html( $s['title'] ); ?></h2></div><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">View all cars →</a></div><div class="df-category-grid"><?php foreach ( $s['items'] as $item ) : ?><a class="df-category-card" href="<?php echo esc_url( $fleet ); ?>"><img src="<?php echo esc_url( $item['image']['url'] ?? '' ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>"><h3><?php echo esc_html( $item['title'] ); ?></h3><p><?php echo esc_html( $item['text'] ); ?></p><span>From <strong class="df-price"><?php echo esc_html( $item['icon'] ); ?></strong> / day</span></a><?php endforeach; ?></div></div></section><?php break;
			case 'offers': ?>
				<section class="df-section" id="deals"><div class="df-container df-promos"><article class="df-promo"><img src="<?php echo esc_url( $s['first_image']['url'] ); ?>" alt="Rental offer"><div class="df-promo-copy"><small>Weekend Special</small><h2><?php echo esc_html( $s['first_title'] ); ?></h2><p><?php echo esc_html( $s['first_text'] ); ?></p><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">Book now</a></div></article><article class="df-promo"><img src="<?php echo esc_url( $s['second_image']['url'] ); ?>" alt="Long-term rental offer"><div class="df-promo-copy"><small>Long Term Rentals</small><h2><?php echo esc_html( $s['second_title'] ); ?></h2><p><?php echo esc_html( $s['second_text'] ); ?></p><a class="df-button" href="<?php echo esc_url( $fleet ); ?>">Learn more</a></div></article></div></section><?php break;
			case 'deals': ?>
				<section class="df-section df-deals-section"><div class="df-container"><div class="df-section-heading"><h2><?php echo esc_html( $s['title'] ); ?></h2><a class="df-outline-button" href="<?php echo esc_url( $fleet ); ?>">View all deals →</a></div><div class="df-deal-grid"><?php foreach ( $s['items'] as $item ) : ?><a class="df-deal-card" href="<?php echo esc_url( $fleet ); ?>"><img src="<?php echo esc_url( $item['image']['url'] ?? '' ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>"><h3><?php echo esc_html( $item['title'] ); ?></h3><strong><?php echo esc_html( $item['icon'] ); ?></strong><small> / day</small></a><?php endforeach; ?></div></div></section><?php break;
			case 'steps': ?>
				<section class="df-section" id="how-it-works"><div class="df-container"><div class="df-section-heading"><div><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h2><?php echo esc_html( $s['title'] ); ?></h2></div></div><div class="df-steps"><?php foreach ( $s['items'] as $item ) : ?><article class="df-step"><span><?php $this->marker( $item ); ?></span><div><h3><?php echo esc_html( $item['title'] ); ?></h3><p><?php echo esc_html( $item['text'] ); ?></p></div></article><?php endforeach; ?></div></div></section><?php break;
			case 'benefits': ?>
				<section class="df-section"><div class="df-container df-benefits-layout"><div><div class="df-section-heading"><h2><?php echo esc_html( $s['title'] ); ?></h2></div><div class="df-benefit-grid"><?php foreach ( $s['items'] as $item ) : ?><article class="df-benefit"><span><?php echo esc_html( $item['icon'] ); ?></span><p><?php echo esc_html( $item['text'] ); ?></p><strong><?php echo esc_html( $item['title'] ); ?></strong></article><?php endforeach; ?></div></div><aside class="df-mobile-callout"><img src="<?php echo esc_url( $s['callout_image']['url'] ); ?>" alt="DriveFlex rental car"><div><h2><?php echo esc_html( $s['callout_title'] ); ?></h2><p><?php echo esc_html( $s['callout_text'] ); ?></p><a class="df-outline-button" href="<?php echo esc_url( $fleet ); ?>">Explore on mobile →</a></div></aside></div></section><?php break;
			case 'contact-hero': ?>
				<section class="df-contact-hero"><div class="df-container"><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h1><?php echo esc_html( $s['title'] ); ?></h1><p><?php echo esc_html( $s['intro'] ); ?></p></div></section><?php break;
			case 'contact-main': $wa = $s['whatsapp']; ?>
				<section class="df-container df-contact-layout"><div class="df-contact-form-card"><span class="df-eyebrow">SEND A MESSAGE</span><h2><?php echo esc_html( $s['title'] ); ?></h2><form class="df-contact-form" data-whatsapp="<?php echo esc_url( $wa ); ?>"><div class="df-contact-fields"><label>First name<input name="firstName" required></label><label>Last name<input name="lastName" required></label><label class="df-wide">Email address<input name="email" type="email" required></label><label class="df-wide">Phone number<input name="phone" type="tel" required></label><label class="df-wide">Message<textarea name="message" rows="6" required></textarea></label></div><p class="df-form-error"></p><button class="df-button" type="submit">Send message on WhatsApp →</button></form></div><aside class="df-contact-details"><article><b>☎</b><div><h3>Phone &amp; WhatsApp</h3><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $s['phone'] ) ); ?>"><?php echo esc_html( $s['phone'] ); ?></a><a href="<?php echo esc_url( $wa ); ?>">Start a WhatsApp chat →</a></div></article><article><b>⌖</b><div><h3>Service area</h3><p><?php echo esc_html( $s['location'] ); ?></p></div></article></aside></section><section class="df-container df-contact-map"><iframe title="DriveFlex location" src="https://www.google.com/maps?q=<?php echo rawurlencode( $s['location'] ); ?>&amp;output=embed" loading="lazy"></iframe></section><?php break;
			case 'contact-faq': ?>
				<section class="df-contact-faq"><div class="df-container"><span class="df-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><h2><?php echo esc_html( $s['title'] ); ?></h2><?php foreach ( $s['items'] as $index => $item ) : ?><details <?php echo 0 === $index ? 'open' : ''; ?>><summary><?php echo esc_html( $item['title'] ); ?></summary><p><?php echo esc_html( $item['text'] ); ?></p></details><?php endforeach; ?></div></section><?php break;
		}
	}
}

final class DriveFlex_Hero_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'hero'; }
final class DriveFlex_Categories_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'categories'; }
final class DriveFlex_Offers_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'offers'; }
final class DriveFlex_Deals_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'deals'; }
final class DriveFlex_Steps_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'steps'; }
final class DriveFlex_Benefits_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'benefits'; }
final class DriveFlex_Contact_Hero_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'contact-hero'; }
final class DriveFlex_Contact_Main_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'contact-main'; }
final class DriveFlex_Contact_Faq_Section extends DriveFlex_Elementor_Section { protected const SECTION = 'contact-faq'; }
