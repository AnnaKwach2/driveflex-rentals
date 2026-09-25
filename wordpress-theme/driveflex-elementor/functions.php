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

function driveflex_elementor_page(): bool {
	return did_action( 'elementor/loaded' ) && is_singular() && class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() );
}

function driveflex_option( string $key, string $default = '' ): string {
	return sanitize_text_field( (string) get_theme_mod( $key, $default ) );
}

function driveflex_customizer( WP_Customize_Manager $customizer ): void {
	$customizer->add_section( 'driveflex_business', array( 'title' => __( 'DriveFlex business details', 'driveflex-elementor' ), 'priority' => 30 ) );
	$fields = array(
		'driveflex_phone' => array( 'Phone', '+254 700 000 000' ), 'driveflex_email' => array( 'Email', 'bookings@example.com' ),
		'driveflex_location' => array( 'Location', 'Nairobi, Kenya' ), 'driveflex_facebook' => array( 'Facebook URL', 'https://facebook.com/' ),
		'driveflex_instagram' => array( 'Instagram URL', 'https://instagram.com/' ), 'driveflex_linkedin' => array( 'LinkedIn URL', 'https://linkedin.com/' ),
		'driveflex_x' => array( 'X URL', 'https://x.com/' ),
	);
	foreach ( $fields as $id => $field ) {
		$customizer->add_setting( $id, array( 'default' => $field[1], 'sanitize_callback' => str_contains( $id, 'facebook' ) || str_contains( $id, 'instagram' ) || str_contains( $id, 'linkedin' ) || 'driveflex_x' === $id ? 'esc_url_raw' : 'sanitize_text_field' ) );
		$customizer->add_control( $id, array( 'label' => $field[0], 'section' => 'driveflex_business' ) );
	}
}
add_action( 'customize_register', 'driveflex_customizer' );

function driveflex_default_menu(): void {
	echo '<ul class="df-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li><li><a href="' . esc_url( home_url( '/fleet/' ) ) . '">Fleet</a></li><li><a href="' . esc_url( home_url( '/#how-it-works' ) ) . '">How it works</a></li><li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></li></ul>';
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
		'contact' => array( 'title' => 'Contact', 'content' => '<!-- wp:heading --><h2>Plan your journey</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Tell us where you are travelling, your preferred dates and the vehicle you need. Our team will confirm availability and collection arrangements.</p><!-- /wp:paragraph -->' ),
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

function driveflex_plugin_notice(): void {
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'DriveFlex_Plugin' ) ) {
		echo '<div class="notice notice-warning"><p><strong>DriveFlex theme:</strong> install and activate the DesignPhox DriveFlex Theme Fleet &amp; Booking plugin to display the fleet and accept booking requests.</p></div>';
	}
}
add_action( 'admin_notices', 'driveflex_plugin_notice' );
