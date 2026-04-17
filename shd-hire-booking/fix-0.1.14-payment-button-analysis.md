# SHD Hire Booking Unified fix 0.1.14

## Goal
Replace fragile post-submit auto redirect with a clearly visible payment button after form submission.

## Analysis
- The resolved Stripe checkout URL itself was not the main issue.
- The unstable step was the immediate redirect after POST.
- A minimum-diff, robust fix is to preserve the existing booking save / email flow but render a payment button in the post-submit message instead of redirecting.

## Changes
- `get_message_html()` now supports optional action button data.
- When a checkout URL is resolved, the plugin stores a success message with:
  - action_url
  - action_label
  - subtext
- Auto redirect was removed for this path.
- Small CSS spacing added for the message action button.
- Version bumped to 0.1.14.
