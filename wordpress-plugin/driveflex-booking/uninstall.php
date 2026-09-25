<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'driveflex_db_version' );
delete_option( 'driveflex_whatsapp' );
delete_option( 'driveflex_booking_email' );
delete_option( 'driveflex_company_name' );
delete_option( 'driveflex_currency' );
delete_option( 'driveflex_locations' );

// Booking and vehicle records are intentionally preserved to prevent accidental data loss.
