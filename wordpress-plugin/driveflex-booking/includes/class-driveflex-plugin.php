<?php
defined( 'ABSPATH' ) || exit;

final class DriveFlex_Plugin {
	private static ?DriveFlex_Plugin $instance = null;

	public static function instance(): DriveFlex_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function activate(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'driveflex_bookings';
		$collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			reference varchar(24) NOT NULL,
			vehicle_id bigint(20) unsigned NOT NULL,
			customer_name varchar(190) NOT NULL,
			phone varchar(60) NOT NULL,
			email varchar(190) NOT NULL,
			pickup_at datetime NOT NULL,
			dropoff_at datetime NOT NULL,
			pickup_location varchar(190) NOT NULL,
			dropoff_location varchar(190) NOT NULL,
			notes text NOT NULL,
			daily_rate decimal(12,2) NOT NULL DEFAULT 0,
			days int(11) unsigned NOT NULL DEFAULT 0,
			discount decimal(12,2) NOT NULL DEFAULT 0,
			estimated_total decimal(12,2) NOT NULL DEFAULT 0,
			status varchar(32) NOT NULL DEFAULT 'requested',
			staff_notes text NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY reference (reference),
			KEY vehicle_dates (vehicle_id,pickup_at,dropoff_at),
			KEY status (status)
		) $collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'driveflex_db_version', DRIVEFLEX_VERSION );
		if ( false === get_option( 'driveflex_whatsapp', false ) ) {
			add_option( 'driveflex_whatsapp', '254706449960' );
		}
		self::instance()->register_vehicle_type();
		flush_rewrite_rules();
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_vehicle_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_vehicle_meta_box' ) );
		add_action( 'save_post_driveflex_vehicle', array( $this, 'save_vehicle' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_post_driveflex_update_booking', array( $this, 'update_booking' ) );
		add_action( 'admin_post_driveflex_import_fleet', array( $this, 'import_starter_fleet' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_shortcode( 'driveflex_fleet', array( $this, 'fleet_shortcode' ) );
	}

	public function register_vehicle_type(): void {
		$company = $this->company_name();
		register_post_type(
			'driveflex_vehicle',
			array(
				'labels' => array(
					'name' => sprintf( __( '%s Vehicles', 'driveflex-booking' ), $company ),
					'singular_name' => __( 'Vehicle', 'driveflex-booking' ),
					'add_new_item' => __( 'Add Vehicle', 'driveflex-booking' ),
					'edit_item' => __( 'Edit Vehicle', 'driveflex-booking' ),
				),
				'public' => true,
				'show_in_rest' => true,
				'menu_icon' => 'dashicons-car',
				'supports' => array( 'title', 'thumbnail', 'editor' ),
				'has_archive' => true,
				'rewrite' => array( 'slug' => 'fleet' ),
			)
		);
		register_taxonomy(
			'driveflex_category',
			'driveflex_vehicle',
			array(
				'label' => __( 'Vehicle Categories', 'driveflex-booking' ),
				'public' => true,
				'hierarchical' => true,
				'show_in_rest' => true,
			)
		);
	}

	public function add_vehicle_meta_box(): void {
		add_meta_box( 'driveflex_vehicle_details', __( 'Rental Details', 'driveflex-booking' ), array( $this, 'vehicle_meta_box' ), 'driveflex_vehicle', 'normal', 'high' );
	}

	public function vehicle_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'driveflex_save_vehicle', 'driveflex_vehicle_nonce' );
		$fields = array(
			'rate' => array( 'Daily rate (' . $this->currency() . ')', 'number', '0' ),
			'seats' => array( 'Seats', 'number', '5' ),
			'luggage' => array( 'Luggage', 'number', '2' ),
			'doors' => array( 'Doors', 'number', '5' ),
			'transmission' => array( 'Transmission', 'text', 'Automatic' ),
			'minimum_days' => array( 'Minimum rental days', 'number', '3' ),
		);
		echo '<div class="driveflex-meta-grid">';
		foreach ( $fields as $key => $field ) {
			$value = get_post_meta( $post->ID, '_driveflex_' . $key, true );
			if ( '' === $value ) {
				$value = $field[2];
			}
			printf( '<p><label><strong>%s</strong><input style="width:100%%" type="%s" min="0" name="driveflex_%s" value="%s"></label></p>', esc_html( $field[0] ), esc_attr( $field[1] ), esc_attr( $key ), esc_attr( $value ) );
		}
		echo '<p><label><strong>Availability</strong><select style="width:100%" name="driveflex_active"><option value="1" ' . selected( get_post_meta( $post->ID, '_driveflex_active', true ), '1', false ) . '>Available</option><option value="0" ' . selected( get_post_meta( $post->ID, '_driveflex_active', true ), '0', false ) . '>Unavailable</option></select></label></p>';
		echo '</div><style>.driveflex-meta-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 20px}</style>';
	}

	public function save_vehicle( int $post_id ): void {
		if ( ! isset( $_POST['driveflex_vehicle_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['driveflex_vehicle_nonce'] ) ), 'driveflex_save_vehicle' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		$numbers = array( 'rate', 'seats', 'luggage', 'doors', 'minimum_days', 'active' );
		foreach ( $numbers as $key ) {
			if ( isset( $_POST[ 'driveflex_' . $key ] ) ) {
				update_post_meta( $post_id, '_driveflex_' . $key, absint( $_POST[ 'driveflex_' . $key ] ) );
			}
		}
		if ( isset( $_POST['driveflex_transmission'] ) ) {
			update_post_meta( $post_id, '_driveflex_transmission', sanitize_text_field( wp_unslash( $_POST['driveflex_transmission'] ) ) );
		}
	}

	public function register_rest_routes(): void {
		register_rest_route( 'driveflex/v1', '/vehicles', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( $this, 'api_vehicles' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( 'driveflex/v1', '/estimate', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'api_estimate' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( 'driveflex/v1', '/bookings', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'api_booking' ), 'permission_callback' => '__return_true' ) );
	}

	public function api_vehicles(): WP_REST_Response {
		$posts = get_posts( array( 'post_type' => 'driveflex_vehicle', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
		$data = array_map( array( $this, 'vehicle_data' ), $posts );
		return rest_ensure_response( $data );
	}

	private function vehicle_data( WP_Post $post ): array {
		$terms = wp_get_post_terms( $post->ID, 'driveflex_category', array( 'fields' => 'names' ) );
		return array(
			'id' => $post->ID,
			'name' => get_the_title( $post ),
			'description' => wp_strip_all_tags( get_the_excerpt( $post ) ?: $post->post_content ),
			'category' => $terms[0] ?? 'Vehicle',
			'image' => get_the_post_thumbnail_url( $post, 'large' ) ?: '',
			'rate' => (int) get_post_meta( $post->ID, '_driveflex_rate', true ),
			'seats' => (int) get_post_meta( $post->ID, '_driveflex_seats', true ),
			'luggage' => (int) get_post_meta( $post->ID, '_driveflex_luggage', true ),
			'doors' => (int) get_post_meta( $post->ID, '_driveflex_doors', true ),
			'transmission' => get_post_meta( $post->ID, '_driveflex_transmission', true ) ?: 'Automatic',
			'minimum_days' => max( 1, (int) get_post_meta( $post->ID, '_driveflex_minimum_days', true ) ),
			'active' => '1' === (string) get_post_meta( $post->ID, '_driveflex_active', true ),
		);
	}

	private function parse_trip( WP_REST_Request $request ): array|WP_Error {
		$vehicle_id = absint( $request['vehicle_id'] );
		$post = get_post( $vehicle_id );
		if ( ! $post || 'driveflex_vehicle' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new WP_Error( 'invalid_vehicle', __( 'Choose a valid vehicle.', 'driveflex-booking' ), array( 'status' => 400 ) );
		}
		$pickup = $this->date_time( $request['pickup_date'], $request['pickup_time'] );
		$dropoff = $this->date_time( $request['dropoff_date'], $request['dropoff_time'] );
		if ( ! $pickup || ! $dropoff || $pickup < current_datetime() || $dropoff <= $pickup ) {
			return new WP_Error( 'invalid_dates', __( 'Choose valid future pickup and drop-off dates.', 'driveflex-booking' ), array( 'status' => 400 ) );
		}
		$vehicle = $this->vehicle_data( $post );
		if ( ! $vehicle['active'] ) {
			return new WP_Error( 'vehicle_unavailable', __( 'This vehicle is not currently accepting booking requests.', 'driveflex-booking' ), array( 'status' => 409 ) );
		}
		$seconds = $dropoff->getTimestamp() - $pickup->getTimestamp();
		$days = (int) ceil( $seconds / DAY_IN_SECONDS );
		if ( $days < $vehicle['minimum_days'] ) {
			return new WP_Error( 'minimum_days', sprintf( __( 'This vehicle requires at least %d rental days.', 'driveflex-booking' ), $vehicle['minimum_days'] ), array( 'status' => 400 ) );
		}
		$subtotal = $days * $vehicle['rate'];
		$percent = $days >= 30 ? 30 : 0;
		$discount = round( $subtotal * $percent / 100 );
		return compact( 'vehicle', 'pickup', 'dropoff', 'days', 'subtotal', 'percent', 'discount' ) + array( 'total' => $subtotal - $discount );
	}

	private function date_time( mixed $date, mixed $time ): ?DateTimeImmutable {
		$value = sanitize_text_field( (string) $date ) . ' ' . sanitize_text_field( (string) $time );
		$parsed = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $value, wp_timezone() );
		$errors = DateTimeImmutable::getLastErrors();
		if ( ! $parsed instanceof DateTimeImmutable || ( is_array( $errors ) && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) || $parsed->format( 'Y-m-d H:i' ) !== $value ) {
			return null;
		}
		return $parsed;
	}

	public function api_estimate( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$trip = $this->parse_trip( $request );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}
		$available = ! $this->has_overlap( $trip['vehicle']['id'], $trip['pickup'], $trip['dropoff'] );
		return rest_ensure_response( array(
			'vehicle' => $trip['vehicle'], 'days' => $trip['days'], 'daily_rate' => $trip['vehicle']['rate'],
			'subtotal' => $trip['subtotal'], 'discount_percent' => $trip['percent'], 'discount' => $trip['discount'],
			'total' => $trip['total'], 'available' => $available, 'currency' => 'KES',
		) );
	}

	private function has_overlap( int $vehicle_id, DateTimeImmutable $pickup, DateTimeImmutable $dropoff, int $exclude_id = 0 ): bool {
		global $wpdb;
		$table = $wpdb->prefix . 'driveflex_bookings';
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $table WHERE vehicle_id = %d AND id != %d AND status IN ('available','awaiting_payment','confirmed') AND pickup_at < %s AND dropoff_at > %s",
			$vehicle_id, $exclude_id, $dropoff->format( 'Y-m-d H:i:s' ), $pickup->format( 'Y-m-d H:i:s' )
		) );
		return (int) $count > 0;
	}

	public function api_booking( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$trip = $this->parse_trip( $request );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}
		$name = sanitize_text_field( (string) $request['name'] );
		$phone = sanitize_text_field( (string) $request['phone'] );
		$email = sanitize_email( (string) $request['email'] );
		$notes = sanitize_textarea_field( (string) $request['notes'] );
		$pickup_location = sanitize_text_field( (string) $request['pickup_location'] );
		$dropoff_location = sanitize_text_field( (string) $request['dropoff_location'] );
		if ( ! $name || ! $phone || ! is_email( $email ) || ! $notes || ! $pickup_location || ! $dropoff_location || ! rest_sanitize_boolean( $request['terms'] ) ) {
			return new WP_Error( 'missing_details', __( 'Complete all required details and accept the rental terms.', 'driveflex-booking' ), array( 'status' => 400 ) );
		}
		if ( $this->has_overlap( $trip['vehicle']['id'], $trip['pickup'], $trip['dropoff'] ) ) {
			return new WP_Error( 'dates_unavailable', sprintf( __( 'These dates are no longer available. Choose different dates or contact %s.', 'driveflex-booking' ), $this->company_name() ), array( 'status' => 409 ) );
		}
		if ( ! $this->rate_limit() ) {
			return new WP_Error( 'rate_limited', __( 'Please wait before sending another request.', 'driveflex-booking' ), array( 'status' => 429 ) );
		}
		global $wpdb;
		$now = current_time( 'mysql' );
		$reference = 'DF-' . strtoupper( wp_generate_password( 8, false, false ) );
		$inserted = $wpdb->insert(
			$wpdb->prefix . 'driveflex_bookings',
			array(
				'reference' => $reference, 'vehicle_id' => $trip['vehicle']['id'], 'customer_name' => $name,
				'phone' => $phone, 'email' => $email, 'pickup_at' => $trip['pickup']->format( 'Y-m-d H:i:s' ),
				'dropoff_at' => $trip['dropoff']->format( 'Y-m-d H:i:s' ), 'pickup_location' => $pickup_location,
				'dropoff_location' => $dropoff_location, 'notes' => $notes, 'daily_rate' => $trip['vehicle']['rate'],
				'days' => $trip['days'], 'discount' => $trip['discount'], 'estimated_total' => $trip['total'],
				'status' => 'requested', 'created_at' => $now, 'updated_at' => $now,
			),
			array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%f', '%f', '%s', '%s', '%s' )
		);
		if ( false === $inserted ) {
			return new WP_Error( 'save_failed', __( 'The request could not be saved. Please try again.', 'driveflex-booking' ), array( 'status' => 500 ) );
		}
		$this->send_notifications( $reference, $trip, compact( 'name', 'phone', 'email', 'notes', 'pickup_location', 'dropoff_location' ) );
		$message = $this->booking_message( $reference, $trip, compact( 'name', 'phone', 'email', 'notes', 'pickup_location', 'dropoff_location' ) );
		$whatsapp = preg_replace( '/\D+/', '', (string) get_option( 'driveflex_whatsapp', '' ) );
		$url = 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode( $message );
		return new WP_REST_Response( array( 'reference' => $reference, 'status' => 'requested', 'whatsapp_url' => $url, 'message' => sprintf( __( 'Your request has been received. %s will confirm availability.', 'driveflex-booking' ), $this->company_name() ) ), 201 );
	}

	private function rate_limit(): bool {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		$key = 'driveflex_rate_' . md5( wp_salt( 'nonce' ) . $ip );
		$count = (int) get_transient( $key );
		if ( $count >= 5 ) {
			return false;
		}
		set_transient( $key, $count + 1, HOUR_IN_SECONDS );
		return true;
	}

	private function booking_message( string $reference, array $trip, array $customer ): string {
		$company = $this->company_name();
		$currency = $this->currency();
		return sprintf(
			"%s booking request\nReference: %s\nVehicle: %s\nRental: %d days at %s %s per day\nPick-up: %s — %s\nDrop-off: %s — %s\nEstimated total: %s %s\nCustomer: %s\nPhone: %s\nEmail: %s\nRequest details: %s",
			$company, $reference, $trip['vehicle']['name'], $trip['days'], $currency, number_format_i18n( $trip['vehicle']['rate'] ),
			$trip['pickup']->format( 'Y-m-d H:i' ), $customer['pickup_location'], $trip['dropoff']->format( 'Y-m-d H:i' ),
			$customer['dropoff_location'], $currency, number_format_i18n( $trip['total'] ), $customer['name'], $customer['phone'], $customer['email'], $customer['notes']
		);
	}

	private function send_notifications( string $reference, array $trip, array $customer ): void {
		$message = $this->booking_message( $reference, $trip, $customer );
		$admin_email = sanitize_email( (string) get_option( 'driveflex_booking_email', get_option( 'admin_email' ) ) );
		wp_mail( $admin_email, $this->company_name() . ' request ' . $reference, $message );
		wp_mail( $customer['email'], 'We received your ' . $this->company_name() . ' request ' . $reference, "Thank you, {$customer['name']}.\n\nWe received your request and will confirm vehicle availability before payment.\n\n" . $message );
	}

	public function fleet_shortcode(): string {
		wp_enqueue_style( 'driveflex-booking', DRIVEFLEX_URL . 'assets/driveflex-booking.css', array(), DRIVEFLEX_VERSION );
		wp_enqueue_script( 'driveflex-booking', DRIVEFLEX_URL . 'assets/driveflex-booking.js', array(), DRIVEFLEX_VERSION, true );
		wp_localize_script( 'driveflex-booking', 'DriveFlexBooking', array(
			'api' => esc_url_raw( rest_url( 'driveflex/v1' ) ),
			'locations' => $this->locations(),
			'currency' => $this->currency(),
			'company' => $this->company_name(),
			'today' => current_datetime()->format( 'Y-m-d' ),
		) );
		return '<div id="driveflex-booking-app" class="driveflex-app"><p class="driveflex-loading">' . esc_html( sprintf( __( 'Loading the %s fleet…', 'driveflex-booking' ), $this->company_name() ) ) . '</p></div>';
	}

	public function admin_menu(): void {
		add_submenu_page( 'edit.php?post_type=driveflex_vehicle', 'Bookings', 'Bookings', 'manage_options', 'driveflex-bookings', array( $this, 'bookings_page' ) );
		add_submenu_page( 'edit.php?post_type=driveflex_vehicle', 'Settings', 'Settings', 'manage_options', 'driveflex-settings', array( $this, 'settings_page' ) );
	}

	public function bookings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}driveflex_bookings ORDER BY created_at DESC LIMIT 250" );
		echo '<div class="wrap"><h1>' . esc_html( $this->company_name() ) . ' Bookings</h1>';
		if ( isset( $_GET['driveflex_error'] ) && 'overlap' === sanitize_key( wp_unslash( $_GET['driveflex_error'] ) ) ) {
			echo '<div class="notice notice-error"><p>This request overlaps a booking already marked Available, Awaiting Payment or Confirmed. Resolve the existing booking before holding these dates.</p></div>';
		}
		echo '<table class="widefat striped"><thead><tr><th>Reference</th><th>Customer</th><th>Vehicle</th><th>Trip</th><th>Total</th><th>Status</th><th>Update</th></tr></thead><tbody>';
		foreach ( $rows as $row ) {
			$action = admin_url( 'admin-post.php' );
			echo '<tr><td><strong>' . esc_html( $row->reference ) . '</strong><br><small>' . esc_html( $row->created_at ) . '</small></td><td>' . esc_html( $row->customer_name ) . '<br><a href="mailto:' . esc_attr( $row->email ) . '">' . esc_html( $row->email ) . '</a><br>' . esc_html( $row->phone ) . '</td><td>' . esc_html( get_the_title( $row->vehicle_id ) ) . '</td><td>' . esc_html( $row->pickup_at ) . '<br>to ' . esc_html( $row->dropoff_at ) . '</td><td>' . esc_html( $this->currency() . ' ' . number_format_i18n( $row->estimated_total ) ) . '</td><td>' . esc_html( ucwords( str_replace( '_', ' ', $row->status ) ) ) . '</td><td><form method="post" action="' . esc_url( $action ) . '"><input type="hidden" name="action" value="driveflex_update_booking"><input type="hidden" name="booking_id" value="' . absint( $row->id ) . '">';
			wp_nonce_field( 'driveflex_update_booking_' . $row->id );
			echo '<select name="status">';
			foreach ( array( 'requested', 'available', 'unavailable', 'awaiting_payment', 'confirmed', 'completed', 'cancelled' ) as $status ) {
				echo '<option value="' . esc_attr( $status ) . '" ' . selected( $row->status, $status, false ) . '>' . esc_html( ucwords( str_replace( '_', ' ', $status ) ) ) . '</option>';
			}
			echo '</select><button class="button">Save</button></form></td></tr>';
		}
		echo '</tbody></table></div>';
	}

	public function update_booking(): void {
		$id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'driveflex_update_booking_' . $id ) ) {
			wp_die( esc_html__( 'You cannot update this booking.', 'driveflex-booking' ) );
		}
		$allowed = array( 'requested', 'available', 'unavailable', 'awaiting_payment', 'confirmed', 'completed', 'cancelled' );
		$status = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : '';
		if ( in_array( $status, $allowed, true ) ) {
			global $wpdb;
			if ( in_array( $status, array( 'available', 'awaiting_payment', 'confirmed' ), true ) ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}driveflex_bookings WHERE id = %d", $id ) );
				$pickup = $row ? new DateTimeImmutable( $row->pickup_at, wp_timezone() ) : null;
				$dropoff = $row ? new DateTimeImmutable( $row->dropoff_at, wp_timezone() ) : null;
				if ( $row && $this->has_overlap( (int) $row->vehicle_id, $pickup, $dropoff, $id ) ) {
					wp_safe_redirect( admin_url( 'edit.php?post_type=driveflex_vehicle&page=driveflex-bookings&driveflex_error=overlap' ) );
					exit;
				}
			}
			$wpdb->update( $wpdb->prefix . 'driveflex_bookings', array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
		}
		wp_safe_redirect( admin_url( 'edit.php?post_type=driveflex_vehicle&page=driveflex-bookings' ) );
		exit;
	}

	public function register_settings(): void {
		register_setting( 'driveflex_settings', 'driveflex_whatsapp', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'driveflex_settings', 'driveflex_booking_email', array( 'sanitize_callback' => 'sanitize_email' ) );
		register_setting( 'driveflex_settings', 'driveflex_company_name', array( 'sanitize_callback' => 'sanitize_text_field', 'default' => 'DriveFlex Rentals' ) );
		register_setting( 'driveflex_settings', 'driveflex_currency', array( 'sanitize_callback' => 'sanitize_text_field', 'default' => 'KSh' ) );
		register_setting( 'driveflex_settings', 'driveflex_locations', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
	}

	public function settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		echo '<div class="wrap"><h1>DriveFlex Settings</h1><form method="post" action="options.php">';
		settings_fields( 'driveflex_settings' );
		echo '<table class="form-table"><tr><th><label for="driveflex_company_name">Company name</label></th><td><input class="regular-text" id="driveflex_company_name" name="driveflex_company_name" value="' . esc_attr( $this->company_name() ) . '"></td></tr><tr><th><label for="driveflex_currency">Currency label</label></th><td><input class="regular-text" id="driveflex_currency" name="driveflex_currency" value="' . esc_attr( $this->currency() ) . '"><p class="description">Examples: KSh, USD, £.</p></td></tr><tr><th><label for="driveflex_locations">Rental locations</label></th><td><textarea class="large-text" rows="5" id="driveflex_locations" name="driveflex_locations">' . esc_textarea( implode( "\n", $this->locations() ) ) . '</textarea><p class="description">One location per line.</p></td></tr><tr><th><label for="driveflex_whatsapp">WhatsApp number</label></th><td><input class="regular-text" id="driveflex_whatsapp" name="driveflex_whatsapp" value="' . esc_attr( get_option( 'driveflex_whatsapp', '254706449960' ) ) . '"><p class="description">International format, for example 254706449960.</p></td></tr><tr><th><label for="driveflex_booking_email">Booking email</label></th><td><input class="regular-text" type="email" id="driveflex_booking_email" name="driveflex_booking_email" value="' . esc_attr( get_option( 'driveflex_booking_email', get_option( 'admin_email' ) ) ) . '"></td></tr></table>';
		submit_button();
		echo '</form><hr><h2>DriveFlex starter fleet</h2><p>Load the complete 19-vehicle base fleet for a new DriveFlex-theme website. You can then edit, add or remove vehicles for the client.</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="driveflex_import_fleet">';
		wp_nonce_field( 'driveflex_import_fleet' );
		submit_button( 'Import or update starter fleet', 'secondary', 'submit', false );
		echo '</form></div>';
	}

	private function company_name(): string {
		return sanitize_text_field( (string) get_option( 'driveflex_company_name', 'DriveFlex Rentals' ) ) ?: 'Car Hire';
	}

	private function currency(): string {
		return sanitize_text_field( (string) get_option( 'driveflex_currency', 'KSh' ) ) ?: 'KSh';
	}

	private function locations(): array {
		$defaults = "Nairobi City Centre\nJomo Kenyatta Airport\nWilson Airport\nMombasa";
		$lines = preg_split( '/\R+/', (string) get_option( 'driveflex_locations', $defaults ) );
		$lines = array_values( array_unique( array_filter( array_map( 'sanitize_text_field', $lines ) ) ) );
		return $lines ?: array( 'Main Office' );
	}

	public function import_starter_fleet(): void {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'driveflex_import_fleet' ) ) {
			wp_die( esc_html__( 'You cannot import the fleet.', 'driveflex-booking' ) );
		}
		foreach ( $this->starter_fleet() as $vehicle ) {
			$post = get_page_by_path( $vehicle['slug'], OBJECT, 'driveflex_vehicle' );
			$post_id = wp_insert_post( array(
				'ID' => $post ? $post->ID : 0,
				'post_type' => 'driveflex_vehicle',
				'post_status' => 'publish',
				'post_name' => $vehicle['slug'],
				'post_title' => $vehicle['name'],
				'post_content' => sprintf( '%s available from DriveFlex Rentals for self-drive and chauffeur-driven journeys across Kenya.', $vehicle['name'] ),
			), true );
			if ( is_wp_error( $post_id ) ) {
				continue;
			}
			wp_set_object_terms( $post_id, $vehicle['category'], 'driveflex_category' );
			foreach ( array( 'rate', 'seats', 'luggage', 'doors', 'minimum_days', 'active' ) as $key ) {
				update_post_meta( $post_id, '_driveflex_' . $key, $vehicle[ $key ] );
			}
			update_post_meta( $post_id, '_driveflex_transmission', 'Automatic' );
			if ( ! has_post_thumbnail( $post_id ) ) {
				$this->attach_starter_image( $post_id, $vehicle['image'], $vehicle['name'] );
			}
		}
		wp_safe_redirect( admin_url( 'edit.php?post_type=driveflex_vehicle&driveflex_imported=1' ) );
		exit;
	}

	private function attach_starter_image( int $post_id, string $filename, string $title ): void {
		$source = DRIVEFLEX_PATH . 'assets/vehicles/' . basename( $filename );
		if ( ! is_readable( $source ) ) {
			return;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$temp = wp_tempnam( $filename );
		if ( ! $temp || ! copy( $source, $temp ) ) {
			return;
		}
		$attachment_id = media_handle_sideload( array( 'name' => $filename, 'tmp_name' => $temp ), $post_id, $title );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $temp );
			return;
		}
		set_post_thumbnail( $post_id, $attachment_id );
	}

	private function starter_fleet(): array {
		$rows = array(
			array( 'tx', 'Toyota Prado TX', 'SUV', 12000, 5, 4, 5, '2021tx.webp' ),
			array( 'vitz', 'Toyota Vitz', 'Compact', 3500, 5, 2, 5, 'vitz.webp' ),
			array( 'rav4', 'Toyota Rav4', 'SUV', 8000, 5, 4, 5, 'rav4.webp' ),
			array( 'axio', 'Toyota Axio', 'Sedan', 4000, 5, 3, 4, 'axio.webp' ),
			array( 'demio', 'Mazda Demio', 'Compact', 3500, 5, 2, 5, 'demio.webp' ),
			array( 'sclass', 'Mercedes S-Class', 'Luxury Sedan', 50000, 5, 2, 5, 'merc-sclass.webp' ),
			array( 'hiace', 'Toyota Hiace', 'Van', 15000, 14, 8, 4, 'hiace.webp' ),
			array( 'cx5', 'Mazda CX-5', 'SUV', 7000, 5, 4, 5, 'mazda-CX5.webp' ),
			array( 'lc300', 'Toyota Land Cruiser LC300', 'Luxury SUV', 70000, 5, 4, 5, 'lc-300.webp' ),
			array( 'harrier', 'Toyota Harrier', 'SUV', 8000, 5, 4, 5, 'harrier.webp' ),
			array( 'voxy', 'Toyota Noah/Voxy', 'Mini Van', 7000, 7, 2, 5, 'voxy.webp' ),
			array( 'lc200', 'Toyota Land Cruiser LC200', 'Luxury SUV', 25000, 5, 4, 5, 'lc-200.webp' ),
			array( 'alphard', 'Toyota Alphard/Vellfire', 'Mini Van', 15000, 7, 2, 5, 'alphard.webp' ),
			array( 'c200', 'Mercedes C-200', 'Luxury Sedan', 15000, 5, 2, 5, 'merc-c200.webp' ),
			array( 'coaster', 'Toyota Coaster', 'Bus', 20000, 24, 15, 4, 'coaster1.webp' ),
			array( 'e200', 'Mercedes E-200', 'Luxury Sedan', 25000, 5, 2, 5, 'merc-e200.webp' ),
			array( 'xtrail', 'Nissan Xtrail', 'SUV', 7000, 5, 4, 5, 'xtrail.webp' ),
			array( 'viano', 'Mercedes Viano', 'Luxury Mini Van', 45000, 7, 5, 5, 'merc-viano.webp' ),
			array( 'hilux', 'Toyota Hilux', 'Pickup Truck', 10000, 5, 10, 4, 'hillux.webp' ),
		);
		return array_map( static function ( array $row ): array {
			return array_combine( array( 'slug', 'name', 'category', 'rate', 'seats', 'luggage', 'doors', 'image' ), $row ) + array( 'minimum_days' => 3, 'active' => 1 );
		}, $rows );
	}
}
