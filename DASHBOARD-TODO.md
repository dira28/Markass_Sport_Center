# Admin Dashboard 5-Feature Implementation - TODO

## Status: 🚀 Starting

### 1. [ ] Create DASHBOARD-TODO.md ✅ DONE

### 2. ✅ Fix ExpirePendingBookings.php
   - payment_deadline > now() + status='pending' → 'expired'
   - Test: `php artisan app:expire-pending-bookings`

   ```
   - Check payment_deadline > now() AND status='pending'
   - PATCH to 'expired'
   - Every 5min scheduler
   ```

### 3. [ ] AdminDashboardController.php
   ```
   - KPI from ALL bookings (no status filter)
   - totalBooking = count(*)
   - totalUsers = unique id_user
   - totalRevenue = sum(total_harga)
   - Call expire check on load
   ```

### 4. [ ] Create LaporanController + view
   ```
   app/Http/Controllers/LaporanController.php
   resources/views/admin/pages/laporan.blade.php
   GET /admin/laporan?from_date&to_date
   ```

### 5. [ ] Add PDF Export
   ```
   composer require barryvdh/laravel-dompdf
   LaporanController@exportPdf()
   ```

### 6. [ ] Add Realtime Polling JS
   ```
   admin-dashboard.js → poll /admin/kpi-data every 10s
   ```

### 7. [ ] Test & Complete ✅

**Next:** Update ExpirePendingBookings.php
