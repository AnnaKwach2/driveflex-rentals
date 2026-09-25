<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'driveflex-elementor' ); ?></a>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
<div class="df-topbar"><div class="df-container"><span>⌖ <?php echo esc_html( driveflex_option( 'driveflex_location', 'Nairobi, Kenya' ) ); ?></span><span>✉ <?php echo esc_html( driveflex_option( 'driveflex_email', 'bookings@example.com' ) ); ?></span><span>☎ <?php echo esc_html( driveflex_option( 'driveflex_phone', '+254 706 449960' ) ); ?></span></div></div>
<header class="df-header">
	<div class="df-container df-header-row">
		<div><?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?><a class="df-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">Drive<span>Flex</span><small>RENTALS</small></a><?php } ?></div>
		<button class="df-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">Menu</button>
		<nav id="primary-navigation" class="df-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'driveflex-elementor' ); ?>">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'df-menu', 'fallback_cb' => 'driveflex_default_menu' ) ); ?>
		</nav>
		<a class="df-button" href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Book now</a>
	</div>
</header>
<?php endif; ?>
<main id="main-content" class="df-main">
