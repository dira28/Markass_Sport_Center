# TODO - Admin Responsiveness

## Step 1: Add mobile-safe admin layout rules
- [x] Update `resources/css/admin/components/sidebar.css` with media queries and off-canvas/collapsible behavior.
- [x] Update `resources/css/admin/components/navbar.css` with mobile typography/padding/overflow fixes.
- [x] Ensure main content doesn’t overlap fixed sidebar on mobile.



## Step 2: Make admin tables responsive (horizontal scroll on mobile)
- [x] Wrap tables with responsive container in:
  - [x] `resources/views/admin/pages/booking.blade.php`
  - [x] `resources/views/admin/pages/laporan.blade.php`
  - [x] `resources/views/admin/components/latest-booking.blade.php`
- [x] Add/adjust CSS in `resources/css/admin/pages/dashboard.css` (and/or other existing admin CSS) to support responsive table container.


## Step 3: Make admin forms stack properly on mobile
- [x] Update `resources/views/admin/pages/laporan.blade.php` filter form + buttons for mobile stacking.

- [x] Add breakpoints in `resources/css/admin/pages/booking.css` for filter controls.


## Step 4: Make dashboard/cards responsive
- [x] Update `resources/css/admin/pages/dashboard.css` for KPI cards, chart filter buttons, padding.


## Step 5: Verify
- [ ] Manual visual checks: desktop/tablet/mobile
- [ ] Check for horizontal scroll behavior only in responsive table containers


