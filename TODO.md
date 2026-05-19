# TODO

## Booking time-slot UI (Traveloka/Ticket style)
- [x] Inspect existing booking UI components (booking-summary, booking.js, booking.css)
- [ ] Update slot locking logic to match exact core rules (pending/ waiting_confirmation/ confirmed + current time < end_time; expired/completed/ current time > end_time => available)
- [ ] Implement auto expire (pending + created_at > 30 minutes => expired, unlock immediately)
- [ ] Implement auto completed (current time > end_time => completed, unlock immediately)
- [ ] Ensure disabled slots are truly unclickable (disabled attribute + guard in click handler)
- [ ] Add clear visual states: AVAILABLE vs BOOKED (opacity, cursor, label “BOOKED”)
- [ ] Ensure only one selection at a time and selected range highlighted
- [ ] Run quick manual test: select lapangan/date, verify booked slots disabled, verify unlocking after time passes

