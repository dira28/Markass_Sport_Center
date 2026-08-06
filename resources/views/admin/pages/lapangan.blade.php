@extends('layouts.admin')

@section('content')

    <div class="container mt-4">

        <h4 class="mb-3">Manajemen Lapangan</h4>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        {{-- Summary Cards (Always shows total dataset metrics, not paginated count) --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card p-3 text-center shadow-sm border-0">
                    <small class="text-muted fw-bold">TOTAL LAPANGAN</small>
                    <h3 class="fw-bold mt-2 mb-0">
                        {{ $totalLapanganCount ?? (method_exists($lapangan, 'total') ? $lapangan->total() : (is_countable($lapangan) ? count($lapangan) : 0)) }}
                    </h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center shadow-sm border-0">
                    <small class="text-muted fw-bold">TOTAL BOOKING</small>
                    <h3 class="fw-bold mt-2 mb-0">{{ $totalBookingCount ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center shadow-sm border-0">
                    <small class="text-muted fw-bold">TOTAL REVENUE</small>
                    <h3 class="fw-bold text-primary mt-2 mb-0">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center shadow-sm border-0">
                    <small class="text-muted fw-bold">STATUS PAID</small>
                    <h3 class="fw-bold text-success mt-2 mb-0">{{ $statusPaidCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Lapangan</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus"></i> Tambah Lapangan
            </button>
        </div>

        {{-- Data Table --}}
        <div class="card shadow-sm border-0 p-3 mb-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lapangan as $item)
                            <tr>
                                <td>{{ $item['nama_lapangan'] }}</td>
                                <td>
                                    Rp {{ number_format($item['harga_pagi'] ?? 0) }} (Pagi)<br>
                                    Rp {{ number_format($item['harga_malam'] ?? 0) }} (Malam)
                                </td>
                                <td>{{ $item['diskon_persen'] ?? 0 }}%</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $item['id_lapangan'] }}">
                                        Edit
                                    </button>

                                    <a href="{{ route('lapangan.delete', $item['id_lapangan']) }}" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editModal{{ $item['id_lapangan'] }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('lapangan.update', $item['id_lapangan']) }}">
                                        @csrf
                                        <div class="modal-content p-3">
                                            <h5 class="mb-3">Edit Lapangan</h5>

                                            <input class="form-control mb-2" name="nama_lapangan"
                                                value="{{ $item['nama_lapangan'] }}" required>
                                            <input class="form-control mb-2" name="harga_pagi"
                                                placeholder="Harga Pagi (07:00 - 16:00)"
                                                value="{{ $item['harga_pagi'] ?? '' }}">
                                            <input class="form-control mb-2" name="harga_malam"
                                                placeholder="Harga Malam (16:00 - 24:00)"
                                                value="{{ $item['harga_malam'] ?? '' }}">
                                            <input class="form-control mb-2" name="diskon_persen"
                                                value="{{ $item['diskon_persen'] }}">
                                            <textarea class="form-control mb-2"
                                                name="deskripsi">{{ $item['deskripsi'] }}</textarea>

                                            <button type="submit" class="btn btn-success mt-2">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data lapangan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Pagination Links --}}
            @if(is_object($lapangan) && method_exists($lapangan, 'links') && $lapangan->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <div class="text-muted small">
                        Menampilkan {{ $lapangan->firstItem() }} - {{ $lapangan->lastItem() }} dari {{ $lapangan->total() }}
                        data
                    </div>
                    <div>
                        {{ $lapangan->links() }}
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('lapangan.store') }}">
                @csrf
                <div class="modal-content p-3">
                    <h5 class="mb-3">Tambah Lapangan</h5>

                    <input class="form-control mb-2" name="nama_lapangan" placeholder="Nama Lapangan" required>
                    <input class="form-control mb-2" name="harga_pagi" placeholder="Harga Pagi (07:00 - 16:00)">
                    <input class="form-control mb-2" name="harga_malam" placeholder="Harga Malam (16:00 - 24:00)">
                    <input class="form-control mb-2" name="diskon_persen" placeholder="Diskon (%)">
                    <textarea class="form-control mb-2" name="deskripsi" placeholder="Deskripsi"></textarea>

                    <button type="submit" class="btn btn-primary mt-2">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection         