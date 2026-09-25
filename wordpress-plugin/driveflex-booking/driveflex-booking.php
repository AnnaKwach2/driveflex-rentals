<?php
/**
 * Plugin Name: DesignPhox DriveFlex Theme Fleet & Booking
 * Description: Fleet management, rental estimates, availability and booking requests for websites built with the DriveFlex car-hire theme.
 * Version: 1.2.0
 * Author: DesignPhox
 * Author URI: https://www.designphox.com
 * Text Domain: driveflex-booking
 * Requires at least: 6.4
 * Requires PHP: 8.1
 */

defined( 'ABSPATH' ) || exit;

define( 'DRIVEFLEX_VERSION', '1.2.0' );
define( 'DRIVEFLEX_FILE', __FILE__ );
define( 'DRIVEFLEX_PATH', plugin_dir_path( __FILE__ ) );
define( 'DRIVEFLEX_URL', plugin_dir_url( __FILE__ ) );

require_once DRIVEFLEX_PATH . 'includes/class-driveflex-plugin.php';

register_activation_hook( __FILE__, array( 'DriveFlex_Plugin', 'activate' ) );
DriveFlex_Plugin::instance();
