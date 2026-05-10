# TODO - Payment System Revision

## Step 1 (Priority): Payment countdown using real `payment_deadline`
- [x] Add `paymentDeadline` hidden input on payment page
- [x] Update `resources/js/payment.js` to compute real countdown every second
- [x] Auto-detect expired payment and disable upload UI with red alert

## Step 2 (Priority): Upload proof API contract fix
- [x] Update `resources/js/payment.js` to use `bukti` form-data key
- [x] Ensure Authorization header uses `Bearer TOKEN`
- [x] Accept only .jpg/.jpeg/.png
- [x] Add image preview before upload
- [x] Disable upload while loading + prevent duplicate upload after success

## Step 3 (Priority): QR image fix
- [x] Render QR using `asset('images/qr-dana.jpg')`

## Step 4: Payment status mapping + badge colors
- [ ] Update `resources/views/user/pages/payment.blade.php` badge to reflect `status_pembayaran` only
- [ ] Update badge colors to match required mapping (pending/yellow, waiting_confirmation/blue, confirmed/green, expired/gray/red, cancelled/dark red)

## Step 5: Booking history page status badges
- [ ] Update `resources/views/user/pages/booking-history.blade.php` to use `status_pembayaran` and same badge mapping

## Step 6: Admin booking management overhaul
- [ ] Fix admin booking table column mapping and alignment
- [ ] Replace “Booked / Available” labels with real `status_pembayaran` (pending/waiting_confirmation/confirmed/expired/cancelled)
- [ ] Add columns + UI: Payment Proof + Verification Action
- [ ] Implement proof preview modal (fallback image)
- [ ] Wire “Approve Payment” to `PATCH /api/booking/:id_booking/confirm-payment`
- [ ] After approval: update status to confirmed, remove approve button, refresh row, show toast

## Step 7: Payment page UI redesign (theme)
- [x] Add dark/navy glassmorphism background + improved card styling
- [ ] Verify selectors vs Blade structure; refine UI spacing/buttons/upload modal/loader

## Step 8: Testing checklist
- [ ] Booking → redirect → payment page loads
- [ ] Countdown hits expired → upload disabled + badge updated
- [ ] Upload proof (jpg/png) → success message + waiting_confirmation badge
- [ ] Admin view proof + approve → confirmed badge on user + admin refresh


