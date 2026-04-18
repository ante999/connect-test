# SHD Hire Booking fix 0.1.16

## Goal
Stabilize service switching, step forms, and Stripe Payment Link transition without touching unrelated areas.

## Findings from the current live page
- The live booking page is loading assets from `/wp-content/plugins/mod/...`.
- The page source shows airport and hourly forms using `shd_payment_method=link`.
- The booking page HTML still contains duplicated `.shd-service-block` structures, which can cause the portal to target the wrong form block.
- Old Stripe front-end assets are still being loaded even though this flow is using Payment Links.

## Minimum-diff changes
- Disabled unnecessary front-end Stripe Elements asset loading in `includes/class-shd-plugin.php`.
- Rebuilt airport form into explicit step groups in `includes/shortcodes/class-shd-shortcodes.php`.
- Rebuilt hourly form into explicit step groups in `includes/shortcodes/class-shd-shortcodes.php`.
- Kept fixed hourly plan selection for 3/4/5/6/7/8 hours, 10h full-day, and Tokyo 1-day.
- Replaced the stepper grouping logic in `assets/js/shd-stepper-forms.js` to prioritize explicit step groups.
- Limited portal service targeting in `assets/js/shd-portal.js` to direct children of the explicit booking portal container.
- Bumped version to 0.1.16.

## Scope control
Only the files directly related to booking portal switching, step forms, and payment-link transition were changed.
