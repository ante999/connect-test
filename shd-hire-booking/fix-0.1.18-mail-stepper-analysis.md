# SHD Hire Booking mod 0.1.18

## Summary
- Kept unrelated areas unchanged.
- Booking flow now requires successful admin notification before payment-link transition.
- Checked booking mail wording across JA/EN/ZH and normalized brand naming.
- Preserved fixed hourly plans and airport/hourly separation.
- Increased text size inside booking inputs.

## Main changes
- shortcodes: mandatory admin notification, redirect only after admin mail success, multilingual wording cleanup
- core: duplicate booking portal auto-injection guard
- plugin assets: unnecessary Stripe Elements front-end loading disabled for Payment Links flow
- stepper js: explicit step groups and portal scoping
- portal js: duplicate portal suppression and first-portal scoping
- forms css: larger inputs and placeholders

## Version
- 0.1.18
