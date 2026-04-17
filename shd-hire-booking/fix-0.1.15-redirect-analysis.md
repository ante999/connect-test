# SHD Hire Booking fix 0.1.15

## Goal
Ensure the user is redirected to the Stripe payment link immediately after booking submission.

## Analysis
- The Stripe return/thank-you URL configuration does not affect the ability to open a Stripe payment link in the first place.
- In 0.1.14, when a checkout URL was resolved, the flow intentionally rendered a post-submit payment button instead of redirecting immediately.
- For production launch, immediate transition to the Stripe payment link is required.

## Minimum-diff change
- In `includes/shortcodes/class-shd-shortcodes.php`, when `checkout_url` is resolved:
  - call `nocache_headers()`
  - call `wp_redirect( $checkout_url, 302 )`
  - `exit`
- Keep the existing button-render fallback only if headers were already sent.
- Version bumped to 0.1.15.
