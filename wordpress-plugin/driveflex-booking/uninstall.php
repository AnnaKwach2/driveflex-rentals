<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'driveflex_db_version' );
delete_option( 'driveflex_whatsapp' );
delete_option( 'driveflex_booking_email' );

// Booking and vehicle records are intentionally preserved to prevent accidental data loss.
