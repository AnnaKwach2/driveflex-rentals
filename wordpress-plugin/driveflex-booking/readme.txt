=== DriveFlex Fleet & Booking ===
Contributors: designphox
Tags: car rental, fleet, booking, kenya
Requires at least: 6.4
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later

Manage DriveFlex vehicles, calculate rental estimates, check availability and receive booking requests.

== Installation ==

1. Upload and activate the plugin.
2. Open DriveFlex Vehicles > Settings and add the booking email and WhatsApp number.
3. Select Import or update starter fleet to load the 19 vehicles from the DriveFlex website, or add vehicles manually.
4. Add the shortcode [driveflex_fleet] to the fleet page.

== Booking workflow ==

Customer requests begin with the Requested status. Staff can mark a request Available, Unavailable, Awaiting Payment, Confirmed, Completed or Cancelled. Available, Awaiting Payment and Confirmed bookings block overlapping availability. ID/passport and payment are intentionally collected after availability confirmation.

== Privacy and operations ==

Booking requests contain customer contact and trip information. Use HTTPS, restrict WordPress administrator accounts, configure a retention policy, and send mail through an authenticated transactional email provider. The plugin does not collect card details or ID/passport numbers.

== Payment integration ==

Version 1.0 manages booking requests and availability confirmation. M-Pesa/card checkout and secure ID upload should be added only after the deposit rules, payment provider account, document retention period and privacy notice are confirmed.
