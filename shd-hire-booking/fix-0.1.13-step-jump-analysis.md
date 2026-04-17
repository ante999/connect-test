# SHD Hire Booking Unified fix 0.1.13

## Issue
Selecting airport/hourly plans and proceeding to the input step could jump to the wrong place.

## Analysis
- Non-form price-table output was accidentally wrapped in `.shd-service-block[data-shd-service]` containers.
- `assets/js/shd-portal.js` scanned the whole content area for service blocks instead of limiting the scan to the explicit booking portal container.
- `assets/js/shd-stepper-forms.js` attached a document-level `[shd_book_now]` click handler once per stepper, so multiple steppers could react to the same click.

## Minimum-diff fix
- Removed accidental service-block wrappers from the price table output.
- Limited service-block detection to `[data-shd-booking-portal="1"]`.
- Removed the duplicate global `[shd_book_now]` integration from `shd-stepper-forms.js`.
- Version bumped to 0.1.13.
