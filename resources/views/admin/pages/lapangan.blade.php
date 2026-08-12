@extends('layouts.admin')

@section('content')

    <div class="container mt-4">

        <h4 class="mb-3">Manajemen Lapangan</h4>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

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
                                <td><strong class="nama-lapangan-item">{{ $item['nama_lapangan'] }}</strong></td>
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
                                        onclick="return confirm('Yakin hapus lapangan ini?')">
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

                                            <label class="form-label small text-muted">Nama Lapangan</label>
                                            <input class="form-control mb-1" name="nama_lapangan"
                                                value="{{ $item['nama_lapangan'] }}" required>

                                            <label class="form-label small text-muted mt-2">Harga Pagi (07:00 - 16:00)</label>
                                            <input class="form-control mb-2" name="harga_pagi" placeholder="Contoh: 50000"
                                                value="{{ $item['harga_pagi'] ?? '' }}">

                                            <label class="form-label small text-muted">Harga Malam (16:00 - 24:00)</label>
                                            <input class="form-control mb-2" name="harga_malam" placeholder="Contoh: 75000"
                                                value="{{ $item['harga_malam'] ?? '' }}">

                                            <label class="form-label small text-muted">Diskon (%)</label>
                                            <input class="form-control mb-2" name="diskon_persen"
                                                value="{{ $item['diskon_persen'] ?? 0 }}">

                                            <label class="form-label small text-muted">Deskripsi</label>
                                            <textarea class="form-control mb-2"
                                                name="deskripsi">{{ $item['deskripsi'] ?? '' }}</textarea>

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
            <form method="POST" action="{{ route('lapangan.store') }}" id="formCreateLapangan">
                @csrf
                <div class="modal-content p-3">
                    <h5 class="mb-3">Tambah Lapangan</h5>

                    <label class="form-label small text-muted">Nama Lapangan</label>
                    <input class="form-control mb-1" id="inputNamaLapangan" name="nama_lapangan" placeholder="Nama Lapangan"
                        required>
                    <div id="duplicateWarning" class="text-danger small mb-2 d-none">
                        ⚠️ Nama lapangan ini sudah ada, gunakan nama lain!
                    </div>

                    <label class="form-label small text-muted">Harga Pagi (07:00 - 16:00)</label>
                    <input class="form-control mb-2" name="harga_pagi" placeholder="Harga Pagi (contoh: 50000)">

                    <label class="form-label small text-muted">Harga Malam (16:00 - 24:00)</label>
                    <input class="form-control mb-2" name="harga_malam" placeholder="Harga Malam (contoh: 75000)">

                    <label class="form-label small text-muted">Diskon (%)</label>
                    <input class="form-control mb-2" name="diskon_persen" placeholder="Diskon (%)">

                    <label class="form-label small text-muted">Deskripsi</label>
                    <textarea class="form-control mb-2" name="deskripsi" placeholder="Deskripsi"></textarea>

                    <button type="submit" id="btnSubmitLapangan" class="btn btn-primary mt-2">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk Validasi Nama Duplikat Secara Real-time --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputNama = document.getElementById('inputNamaLapangan');
                const warningText = document.getElementById('duplicateWarning');
                const btnSubmit = document.getElementById('btnSubmitLapangan');

                if (inputNama) {
                    inputNama.addEventListener('input', function () {
                        const inputVal = this.value.trim().toLowerCase();
                        const existingNames = Array.from(document.querySelectorAll('.nama-lapangan-item'))
                            .map(el => el.textContent.trim().toLowerCase());

                        if (inputVal !== '' && existingNames.includes(inputVal)) {
                            warningText.classList.remove('d-none');
                            btnSubmit.disabled = true;
                        } else {
                            warningText.classList.add('d-none');
                            btnSubmit.disabled = false;
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection