=== DesignPhox DriveFlex Theme Fleet & Booking ===
Contributors: designphox
Tags: car rental, fleet, booking, kenya
Requires at least: 6.4
Requires PHP: 8.1
Stable tag: 1.2.1
License: GPLv2 or later

Fleet management, rental estimates, availability and booking requests for websites built with the DriveFlex car-hire theme.

== Installation ==

1. Upload and activate the plugin.
2. Open DriveFlex Vehicles > Settings and add the client's company name, currency, locations, booking email and WhatsApp number.
3. Select Import or update starter fleet to create the theme's complete base fleet.
4. Add the shortcode [driveflex_fleet] to the fleet page.
5. Edit, add or remove vehicles under the client's Vehicles menu. The theme and booking system read this same fleet automatically.

== Booking workflow ==

Customer requests begin with the Requested status. Staff can mark a request Available, Unavailable, Awaiting Payment, Confirmed, Completed or Cancelled. Available, Awaiting Payment and Confirmed bookings block overlapping availability. ID/passport and payment are intentionally collected after availability confirmation.

== Privacy and operations ==

Booking requests contain customer contact and trip information. Use HTTPS, restrict WordPress administrator accounts, configure a retention policy, and send mail through an authenticated transactional email provider. The plugin does not collect card details or ID/passport numbers.

== Payment integration ==

Version 1.0 manages booking requests and availability confirmation. M-Pesa/card checkout and secure ID upload should be added only after the deposit rules, payment provider account, document retention period and privacy notice are confirmed.

== Theme integration ==

The plugin is the single source of fleet data for the DriveFlex theme. The theme does not maintain a separate hard-coded vehicle list. New client sites can begin with the bundled fleet, then customize vehicles, images, categories, prices and specifications in WordPress. The shortcode and booking flow update automatically from those records.
