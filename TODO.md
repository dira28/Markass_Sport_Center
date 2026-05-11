# TODO

## Payment UI + Admin Connection Refinement

- [ ] 1) Update `resources/css/user/pages/payment.css` (premium hierarchy, QR center square card, upload box/button/preview polish, refined badges).
- [ ] 2) Update `resources/views/user/pages/payment.blade.php` (fix wrappers/classes for QR/upload/status hierarchy).
- [ ] 3) Normalize payment status in `resources/js/payment.js` (map `menunggu_verifikasi` -> `waiting_confirmation`).
- [ ] 4) Update `resources/js/payment.js` upload success flow (badge => `waiting_confirmation`, disable upload on success).
- [ ] 5) Update `resources/views/admin/pages/booking.blade.php` (status mapping + View Proof + Approve Payment buttons).
- [ ] 6) Add proof modal in admin booking table.
- [ ] 7) Normalize status in `resources/views/user/pages/booking-history.blade.php`.
- [ ] 8) Sanity check: grep for `menunggu_verifikasi` usage after changes.

