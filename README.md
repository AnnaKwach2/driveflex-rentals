# DriveFlex Rentals

Responsive Kenyan car-hire website with vehicle categories, rental estimates, and a two-step WhatsApp booking-request flow.

## Local preview

Serve the `dist` directory with any static HTTP server.

## Verification

```sh
node verify.cjs
node --check dist/app.js
node --check dist/rental.js
```

## Deployment

The production site is configured for Vercel with `dist` as the output directory.
