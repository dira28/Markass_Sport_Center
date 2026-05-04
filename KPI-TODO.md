# Admin Dashboard Flexible KPI - TODO

## Status: Starting

### 1. [ ] Create KPI-TODO.md ✅ DONE

### 2. ✅ config/app.php
   - Add `'kpi_include_all' => env('KPI_INCLUDE_ALL', true),`

### 3. ✅ AdminDashboardController.php
   ```
   $includeAll = config('app.kpi_include_all');
   $bookings = collect(API data);
   $filtered = $includeAll ? $bookings : $bookings->filter(fn($b) => $b['status_pembayaran'] === 'confirmed');
   
   KPIs:
   - totalBooking = $filtered->count()
   - totalUser = $filtered->unique('id_user')->count()
   - totalRevenue = $filtered->sum('total_harga')
   - averagePerDay = $filtered->groupBy tanggal → avg
   - today = $filtered->where('tanggal', today())
   ```

### 4. ✅ Test
   ```
   Dev (KPI_INCLUDE_ALL=true):
   - All bookings counted (pending + confirmed)
   - totalUser calculated properly
   - Dashboard reflects ALL test data
   
   Prod (KPI_INCLUDE_ALL=false):
   - Only "confirmed" status_pembayaran
   - Set in .env: KPI_INCLUDE_ALL=false
   ```

### 5. ✅ COMPLETE 🎉

**Usage:**
```
.env:
KPI_INCLUDE_ALL=true    ← Dev (all bookings)
KPI_INCLUDE_ALL=false   ← Prod (confirmed only)

php artisan config:clear
→ Dashboard instantly adapts!
```

**Benefits:**
✅ Single flag controls ALL KPIs
✅ Collections - clean Laravel code
✅ totalUser fixed (unique id_user)
✅ Future-proof for payment gateway
✅ No blade/controller changes needed
