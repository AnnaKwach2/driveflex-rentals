# DriveFlex Fleet & Booking for WordPress

This folder contains the DesignPhox plugin for WordPress websites created from the DriveFlex car-hire theme.

## Version 1.0 scope

- WordPress-managed vehicles, categories, images, daily rates and specifications.
- One-click import for the 19 vehicles currently used by the DriveFlex website.
- Configurable company name, currency, rental locations, email and WhatsApp number.
- Responsive `[driveflex_fleet]` shortcode with category filtering.
- Two-step booking request: trip and server-calculated price, then customer details.
- Nairobi-time date validation, three-day minimum rental and 30% discount for 30+ days.
- Availability checks that prevent staff from holding overlapping bookings.
- Booking references, administrator/customer email notifications and WhatsApp handoff.
- WordPress dashboard statuses: Requested, Available, Unavailable, Awaiting Payment, Confirmed, Completed and Cancelled.
- Name, phone, email, trip notes and acceptance are mandatory. ID/passport and payment are deferred until availability is confirmed.

## Installation

1. In WordPress, open **Plugins > Add New > Upload Plugin**.
2. Upload `designphox-driveflex-booking-1.2.0.zip`, install and activate it.
3. Open **DriveFlex Vehicles > Settings** and set the booking email and WhatsApp number.
4. Select **Import or update starter fleet**, then edit the vehicles for the client.
5. Add `[driveflex_fleet]` to the Fleet page.

Use an SMTP or transactional email plugin on production WordPress so booking messages are authenticated and reliably delivered.

## Architecture

The plugin stores fleet records as a custom post type and booking records in a dedicated WordPress table. Pricing and availability are recalculated on the server; values submitted by a browser are never trusted. Confirmed and payment-stage bookings reserve their vehicle/date range.

The current release is a request-and-confirm workflow. Online M-Pesa/card payment, deposits and private ID uploads should be a second phase after the merchant account, refund rules, deposit percentage, privacy notice and document retention period are decided.

The plugin is the fleet database for every site built from the DriveFlex theme. The theme reads its vehicle records directly, so there is no separate fleet to detect or synchronize. Import the base fleet once on a new site, then customize it for the client in WordPress.

## Size and maintenance

The compressed plugin is expected to remain under 1 MB, including the optimized starter fleet images. It has no JavaScript framework or third-party runtime dependency. WordPress core provides the REST API, database layer, media library and email interface.
