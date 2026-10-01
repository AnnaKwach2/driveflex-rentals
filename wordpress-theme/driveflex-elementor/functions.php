<?php
defined( 'ABSPATH' ) || exit;

function driveflex_theme_setup(): void {
	load_theme_textdomain( 'driveflex-elementor', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support( 'custom-logo', array( 'height' => 90, 'width' => 360, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support( 'rank-math-breadcrumbs' );
	register_nav_menus( array( 'primary' => __( 'Primary navigation', 'driveflex-elementor' ), 'footer' => __( 'Footer navigation', 'driveflex-elementor' ) ) );
}
add_action( 'after_setup_theme', 'driveflex_theme_setup' );

function driveflex_theme_assets(): void {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'driveflex-theme', get_stylesheet_uri(), array(), $version );
	wp_enqueue_script( 'driveflex-theme', get_theme_file_uri( 'assets/js/theme.js' ), array(), $version, array( 'strategy' => 'defer', 'in_footer' => true ) );
}
add_action( 'wp_enqueue_scripts', 'driveflex_theme_assets' );

function driveflex_register_elementor_locations( $manager ): void {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'driveflex_register_elementor_locations' );

function driveflex_register_elementor_widgets( $widgets_manager ): void {
	require_once get_theme_file_path( 'includes/elementor-widgets.php' );
	$widgets_manager->register( new DriveFlex_Home_Elementor_Widget() );
	$widgets_manager->register( new DriveFlex_Contact_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'driveflex_register_elementor_widgets' );

function driveflex_elementor_page(): bool {
	return did_action( 'elementor/loaded' ) && is_singular() && class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() );
}

function driveflex_option( string $key, string $default = '' ): string {
	return sanitize_text_field( (string) get_theme_mod( $key, $default ) );
}

function driveflex_customizer( WP_Customize_Manager $customizer ): void {
	$customizer->add_section( 'driveflex_business', array( 'title' => __( 'DriveFlex business details', 'driveflex-elementor' ), 'priority' => 30 ) );
	$fields = array(
		'driveflex_phone' => array( 'Phone', '+254 706 449960' ), 'driveflex_email' => array( 'Email', 'bookings@example.com' ),
		'driveflex_location' => array( 'Location', 'Nairobi, Kenya' ), 'driveflex_facebook' => array( 'Facebook URL', 'https://facebook.com/' ),
		'driveflex_instagram' => array( 'Instagram URL', 'https://instagram.com/' ), 'driveflex_linkedin' => array( 'LinkedIn URL', 'https://linkedin.com/' ),
		'driveflex_x' => array( 'X URL', 'https://x.com/' ), 'driveflex_whatsapp' => array( 'WhatsApp URL', 'https://wa.me/254706449960' ),
	);
	foreach ( $fields as $id => $field ) {
		$customizer->add_setting( $id, array( 'default' => $field[1], 'sanitize_callback' => str_contains( $id, 'facebook' ) || str_contains( $id, 'instagram' ) || str_contains( $id, 'linkedin' ) || str_contains( $id, 'whatsapp' ) || 'driveflex_x' === $id ? 'esc_url_raw' : 'sanitize_text_field' ) );
		$customizer->add_control( $id, array( 'label' => $field[0], 'section' => 'driveflex_business' ) );
	}
	$customizer->add_setting( 'driveflex_social_size', array( 'default' => 27, 'sanitize_callback' => static fn( $value ) => max( 18, min( 48, absint( $value ) ) ) ) );
	$customizer->add_control( 'driveflex_social_size', array( 'label' => 'Social icon size', 'section' => 'driveflex_business', 'type' => 'range', 'input_attrs' => array( 'min' => 18, 'max' => 48, 'step' => 1 ) ) );
}
add_action( 'customize_register', 'driveflex_customizer' );

function driveflex_social_icon( string $network ): string {
	$icons = array(
		'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M14 8h3V4h-3c-3.3 0-5 2-5 5v2H6v4h3v9h4v-9h3.5l.5-4h-4V9c0-.7.3-1 1-1z"/></svg>',
		'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.25"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
		'linkedin' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><circle cx="5" cy="5" r="2"/><path d="M3.2 9h3.6v12H3.2zM10 9h3.5v1.7c1-1.3 2.3-2.1 4.1-2.1 3 0 4.4 1.9 4.4 5.5V21h-3.7v-6.3c0-1.8-.6-2.8-2.1-2.8-1.7 0-2.5 1.1-2.5 3.3V21H10z"/></svg>',
		'x' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 3l16 18M20 3L4 21"/></svg>',
		'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 11.8a8.5 8.5 0 0 1-12.6 7.4L3 20.5l1.3-4.7a8.5 8.5 0 1 1 16.2-4z"/><path d="M8.2 7.6c.4-.5.8-.4 1.1.1l1 2c.2.4.1.7-.2 1l-.7.7c.8 1.8 2 3 3.8 3.8l.8-.9c.3-.3.6-.4 1-.2l1.9.9c.5.2.6.6.4 1.1-.5 1.1-1.5 1.7-2.8 1.6-3.8-.5-7.8-4.4-8.2-8.2-.1-.8.2-1.4.9-1.9z"/></svg>',
	);
	return $icons[ $network ] ?? '';
}

function driveflex_default_menu(): void {
	echo '<ul class="df-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li><li><a href="' . esc_url( home_url( '/fleet/' ) ) . '">Rent a Car</a></li><li><a href="' . esc_url( home_url( '/#deals' ) ) . '">Deals</a></li><li><a href="' . esc_url( home_url( '/#locations' ) ) . '">Locations</a></li><li><a href="' . esc_url( home_url( '/#how-it-works' ) ) . '">How it works</a></li><li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact Us</a></li></ul>';
}

function driveflex_theme_page_templates( array $templates ): array {
	$templates['templates/elementor-full-width.php'] = __( 'DriveFlex Elementor Full Width', 'driveflex-elementor' );
	return $templates;
}
add_filter( 'theme_page_templates', 'driveflex_theme_page_templates' );

function driveflex_create_starter_pages(): void {
	$pages = array(
		'home' => array( 'title' => 'Home', 'content' => '' ),
		'fleet' => array( 'title' => 'Fleet', 'content' => '<!-- wp:shortcode -->[driveflex_fleet]<!-- /wp:shortcode -->' ),
		'contact' => array( 'title' => 'Contact', 'content' => '<!-- wp:heading --><h2>Contact &amp; Support</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Tell us where you are travelling, your preferred dates and the vehicle you need. Our team will confirm availability and collection arrangements.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><strong>Phone and WhatsApp:</strong> <a href="tel:+254706449960">+254 706 449960</a></p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://wa.me/254706449960">Contact us on WhatsApp</a></div><!-- /wp:button --></div><!-- /wp:buttons -->' ),
	);
	$ids = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		$ids[ $slug ] = $existing ? $existing->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $page['title'], 'post_content' => $page['content'] ) );
	}
	if ( ! empty( $ids['home'] ) && ! is_wp_error( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}
	if ( ! has_nav_menu( 'primary' ) ) {
		$menu_id = wp_create_nav_menu( 'DriveFlex Primary' );
		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( array( 'home', 'fleet', 'contact' ) as $slug ) {
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $pages[ $slug ]['title'], 'menu-item-object' => 'page', 'menu-item-object-id' => $ids[ $slug ], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['primary'] = $menu_id;
			$locations['footer'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
}
add_action( 'after_switch_theme', 'driveflex_create_starter_pages' );

/** Add editable Elementor layouts only when a page has no existing Elementor widgets. */
function driveflex_seed_elementor_pages(): void {
	if ( ! did_action( 'elementor/loaded' ) || ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$layouts = array( 'home' => 'driveflex-home-layout', 'contact' => 'driveflex-contact-layout', 'fleet' => 'shortcode' );
	foreach ( $layouts as $slug => $widget_type ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page || get_post_meta( $page->ID, '_driveflex_elementor_seeded', true ) ) {
			continue;
		}
		$existing = json_decode( (string) get_post_meta( $page->ID, '_elementor_data', true ), true );
		$has_widget = false;
		$walk = static function ( array $elements ) use ( &$walk, &$has_widget ): void {
			foreach ( $elements as $element ) {
				if ( 'widget' === ( $element['elType'] ?? '' ) ) { $has_widget = true; return; }
				if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) { $walk( $element['elements'] ); }
			}
		};
		if ( is_array( $existing ) ) { $walk( $existing ); }
		if ( $has_widget ) {
			update_post_meta( $page->ID, '_driveflex_elementor_seeded', 'preserved-existing-content' );
			continue;
		}
		$widget_settings = 'shortcode' === $widget_type ? array( 'shortcode' => '[driveflex_fleet]' ) : array();
		$data = array( array(
			'id' => substr( md5( 'driveflex-container-' . $slug ), 0, 7 ),
			'elType' => 'container',
			'settings' => array( 'content_width' => 'full', 'padding' => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ), 'gap' => array( 'unit' => 'px', 'size' => 0 ) ),
			'elements' => array( array( 'id' => substr( md5( 'driveflex-widget-' . $slug ), 0, 7 ), 'elType' => 'widget', 'widgetType' => $widget_type, 'settings' => $widget_settings, 'elements' => array() ) ),
			'isInner' => false,
		) );
		update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		update_post_meta( $page->ID, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page->ID, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page->ID, '_wp_page_template', 'templates/elementor-full-width.php' );
		update_post_meta( $page->ID, '_driveflex_elementor_seeded', DRIVEFLEX_ELEMENTOR_CONTENT_VERSION );
		delete_post_meta( $page->ID, '_elementor_css' );
	}
	foreach ( get_pages() as $page ) {
		if ( isset( $layouts[ $page->post_name ] ) || get_post_meta( $page->ID, '_driveflex_elementor_seeded', true ) || '' === trim( $page->post_content ) ) {
			continue;
		}
		$existing = json_decode( (string) get_post_meta( $page->ID, '_elementor_data', true ), true );
		if ( is_array( $existing ) && ! empty( $existing ) ) {
			continue;
		}
		$content = do_blocks( $page->post_content );
		$data = array( array(
			'id' => substr( md5( 'driveflex-container-' . $page->ID ), 0, 7 ), 'elType' => 'container',
			'settings' => array( 'content_width' => 'boxed', 'boxed_width' => array( 'unit' => 'px', 'size' => 900 ), 'padding' => array( 'unit' => 'px', 'top' => '60', 'right' => '20', 'bottom' => '60', 'left' => '20', 'isLinked' => false ) ),
			'elements' => array( array( 'id' => substr( md5( 'driveflex-widget-' . $page->ID ), 0, 7 ), 'elType' => 'widget', 'widgetType' => 'text-editor', 'settings' => array( 'editor' => $content ), 'elements' => array() ) ), 'isInner' => false,
		) );
		update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		update_post_meta( $page->ID, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page->ID, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page->ID, '_wp_page_template', 'templates/elementor-full-width.php' );
		update_post_meta( $page->ID, '_driveflex_elementor_seeded', DRIVEFLEX_ELEMENTOR_CONTENT_VERSION );
		delete_post_meta( $page->ID, '_elementor_css' );
	}
}
define( 'DRIVEFLEX_ELEMENTOR_CONTENT_VERSION', '1.0.6' );
add_action( 'admin_init', 'driveflex_seed_elementor_pages', 40 );

/** Upgrade the untouched starter menu created by earlier theme packages. */
function driveflex_upgrade_starter_menu(): void {
	if ( '1.0.2' === get_option( 'driveflex_theme_content_version' ) ) {
		return;
	}
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$menu_id   = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;
	$items     = $menu_id ? wp_get_nav_menu_items( $menu_id ) : array();
	$titles    = $items ? array_map( static fn( $item ) => strtolower( trim( $item->title ) ), $items ) : array();
	$starter   = $items && count( $items ) <= 3 && ! array_diff( $titles, array( 'home', 'fleet', 'contact' ) );
	if ( $starter ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item->ID, true );
		}
		$links = array(
			array( 'Home', home_url( '/' ) ),
			array( 'Rent a Car', home_url( '/fleet/' ) ),
			array( 'Deals', home_url( '/#deals' ) ),
			array( 'Locations', home_url( '/#locations' ) ),
			array( 'How It Works', home_url( '/#how-it-works' ) ),
			array( 'Contact Us', home_url( '/contact/' ) ),
		);
		foreach ( $links as $link ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $link[0], 'menu-item-url' => $link[1], 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
		}
	}
	update_option( 'driveflex_theme_content_version', '1.0.2' );
}
add_action( 'init', 'driveflex_upgrade_starter_menu', 30 );

function driveflex_plugin_notice(): void {
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'DriveFlex_Plugin' ) ) {
		echo '<div class="notice notice-warning"><p><strong>DriveFlex theme:</strong> install and activate the DesignPhox DriveFlex Theme Fleet &amp; Booking plugin to display the fleet and accept booking requests.</p></div>';
	}
}
add_action( 'admin_notices', 'driveflex_plugin_notice' );
