# DriveFlex WordPress + Elementor package

## Files

- `driveflex-elementor-theme-1.0.1.zip`: install under **Appearance > Themes > Add New**.
- `../wordpress-plugin/designphox-driveflex-booking-1.2.1.zip`: install under **Plugins > Add New**.

## Installation order

1. Install WordPress on HTTPS and set the site language/timezone.
2. Install and activate Elementor, Rank Math SEO and the DriveFlex booking plugin.
3. Install and activate the DriveFlex Elementor theme.
4. The theme creates Home, Fleet and Contact pages plus the primary navigation. It sets Home as the static front page.
5. Open **Vehicles > Settings**, enter the company information, then select **Import or update starter fleet**.
6. Open **Appearance > Customize > DriveFlex business details** for contact and social links.
7. Open a page and select **Edit with Elementor**. Once Elementor content is saved, that content replaces the theme's fallback page design.

## Elementor settings

- Site Settings > Global Colors: Navy `#0b111e`, Slate `#192633`, Orange `#ff650b`, White `#ffffff`, Muted `#727987`.
- Site Settings > Global Fonts: use `Inter, Segoe UI, Roboto, Arial, sans-serif`. This local system-font stack avoids a render-blocking Google Fonts request.
- Page Layout: **DriveFlex Elementor Full Width**.
- Theme Builder (Elementor Pro): header, footer, single, archive and 404 locations are registered. A Theme Builder template automatically replaces the matching theme fallback.
- For entrance motion use Elementor's Fade In Up sparingly. Theme elements also support the class `df-reveal`; motion automatically disables when a visitor requests reduced motion.

## Rank Math

The theme uses WordPress `title-tag` support and does not hard-code descriptions, canonical URLs or schema. Rank Math can therefore manage page titles, meta descriptions, Open Graph images, canonicals and schema without duplicates. Rank Math breadcrumb support is registered.

After importing, run Rank Math's setup wizard, set the organization/logo, connect Search Console if desired, configure Local Business schema and edit each page's SEO title and description.

## Recommended plugin roles

- Elementor: page layout and visual content.
- Rank Math: SEO metadata, sitemap and schema.
- DriveFlex Fleet & Booking: vehicles, rates, availability and booking requests.
- An SMTP/transactional mail plugin: reliable booking email delivery.
- A caching/image-optimization plugin supported by the host: page caching, WebP/AVIF and CDN.
- A backup/security plugin selected for the client's host.

Avoid installing multiple plugins that perform the same caching, SEO, image optimization or security function.

## Performance

The theme has no framework, jQuery or remote font dependency. Images are optimized WebP files, scripts load deferred, below-fold images remain lazy-loadable, animation uses transforms/opacity, and reduced-motion preferences are respected. Elementor's optimized asset loading and improved CSS loading options should remain enabled after compatibility testing.
