# DriveFlex Fleet & Booking for WordPress

This folder contains the DesignPhox plugin for WordPress websites created from the DriveFlex car-hire theme.

## Version 1.0 scope

- WordPress-managed vehicles, categories, images, daily rates and specifications.
- One-click import for the 19 vehicles currently used by the DriveFlex website.
- Configurable company name, currency, rental locations, email and WhatsApp number.
- Responsive `[driveflex_fleet]` shortcode with category filtering.
- Two-step booking request: service type, trip and server-calculated price, then customer details.
- Self Drive requires the configured minimum rental period; With Driver accepts one-day requests.
- Intended area of use, organization bookings, optional discount codes and service-specific declarations.
- Mandatory driver’s licence number and future expiry date for Self Drive; these fields remain hidden for With Driver.
- Nairobi-time date validation, self-drive minimum rental and 30% discount for 30+ days.
- Availability checks that prevent staff from holding overlapping bookings.
- Booking references, administrator/customer email notifications and WhatsApp handoff.
- WordPress dashboard statuses: Requested, Available, Unavailable, Awaiting Payment, Confirmed, Completed and Cancelled.
- Name, phone, email, intended area, trip notes and acceptance are mandatory. Organization name and licence eligibility are conditionally required. ID/passport and payment are deferred until availability is confirmed.

## Installation

1. In WordPress, open **Plugins > Add New > Upload Plugin**.
2. Upload `designphox-driveflex-booking-1.2.8.zip`, install and activate it.
3. Open **DriveFlex Vehicles > Settings** and set the booking email and WhatsApp number.
4. Select **Import or update starter fleet**, then edit the vehicles for the client.
5. In Elementor, drag **DriveFlex Fleet** onto the Fleet page. The shortcode `[driveflex_fleet]` remains available for the block editor.

Use an SMTP or transactional email plugin on production WordPress so booking messages are authenticated and reliably delivered.

## Architecture

The plugin stores fleet records as a custom post type and booking records in a dedicated WordPress table. Pricing and availability are recalculated on the server; values submitted by a browser are never trusted. Confirmed and payment-stage bookings reserve their vehicle/date range.

The current release is a request-and-confirm workflow. Online M-Pesa/card payment, deposits and private ID uploads should be a second phase after the merchant account, refund rules, deposit percentage, privacy notice and document retention period are decided.

The plugin is the fleet database for every site built from the DriveFlex theme. The theme reads its vehicle records directly, so there is no separate fleet to detect or synchronize. Import the base fleet once on a new site, then customize it for the client in WordPress.

The public Fleet page uses the `/fleet/` page containing `[driveflex_fleet]`. Individual vehicle records use `/vehicle/...`; the plugin does not register a competing `/fleet/` archive.

## Size and maintenance

The compressed plugin is expected to remain under 1 MB, including the optimized starter fleet images. It has no JavaScript framework or third-party runtime dependency. WordPress core provides the REST API, database layer, media library and email interface.
