=== DesignPhox Car Hire Fleet & Booking ===
Contributors: designphox
Tags: car rental, fleet, booking, kenya
Requires at least: 6.4
Requires PHP: 8.1
Stable tag: 1.1.0
License: GPLv2 or later

Reusable car-hire fleet management, fleet migration, rental estimates, availability and booking requests.

== Installation ==

1. Upload and activate the plugin.
2. Open DriveFlex Vehicles > Settings and add the company name, currency, locations, booking email and WhatsApp number.
3. Open Import Fleet to migrate an existing WordPress/WooCommerce fleet or upload a CSV. DriveFlex can instead use its bundled starter fleet.
4. Add the shortcode [driveflex_fleet] to the fleet page.

== Booking workflow ==

Customer requests begin with the Requested status. Staff can mark a request Available, Unavailable, Awaiting Payment, Confirmed, Completed or Cancelled. Available, Awaiting Payment and Confirmed bookings block overlapping availability. ID/passport and payment are intentionally collected after availability confirmation.

== Privacy and operations ==

Booking requests contain customer contact and trip information. Use HTTPS, restrict WordPress administrator accounts, configure a retention policy, and send mail through an authenticated transactional email provider. The plugin does not collect card details or ID/passport numbers.

== Payment integration ==

Version 1.0 manages booking requests and availability confirmation. M-Pesa/card checkout and secure ID upload should be added only after the deposit rules, payment provider account, document retention period and privacy notice are confirmed.

== Fleet migration ==

The importer discovers post types registered by the current WordPress website. Select the post type holding the current fleet and map its price/specification custom fields once. The plugin copies the titles, descriptions, featured images and mapped specifications without requiring each vehicle to be recreated. WooCommerce uses the product post type and normally stores its price in _price.

For fleets outside WordPress, upload a CSV with name, category, rate, seats, luggage, doors, transmission and image_url columns. Name and rate are required.
