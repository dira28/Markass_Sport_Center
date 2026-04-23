@extends('layouts.admin')

@section('content')

    <div class="container mt-4">

        <h4>Manajemen Lapangan</h4>

        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- BUTTON CREATE --}}
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
            + Tambah Lapangan
        </button>

        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Diskon</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($lapangan as $item)
                    <tr>
                        <td>{{ $item['nama_lapangan'] }}</td>
                        <td>
                            Rp {{ number_format($item['harga_pagi'] ?? 0) }} (Pagi)<br>
                            Rp {{ number_format($item['harga_malam'] ?? 0) }} (Malam)
                        </td>
                        <td>{{ $item['diskon_persen'] ?? 0 }}%</td>

                        <td>
                            <!-- EDIT -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $item['id_lapangan'] }}">
                                Edit
                            </button>

                            <!-- DELETE -->
                            <a href="{{ route('lapangan.delete', $item['id_lapangan']) }}" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin hapus?')">
                                Hapus
                            </a>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="editModal{{ $item['id_lapangan'] }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('lapangan.update', $item['id_lapangan']) }}">
                                @csrf

                                <div class="modal-content p-3">
                                    <h5>Edit Lapangan</h5>

                                    <input class="form-control mb-2" name="nama_lapangan" value="{{ $item['nama_lapangan'] }}">
                                    <input class="form-control mb-2" name="harga_pagi" placeholder="Harga Pagi (07:00 - 16:00)"
                                        value="{{ $item['harga_pagi'] ?? '' }}">

                                    <input class="form-control mb-2" name="harga_malam"
                                        placeholder="Harga Malam (16:00 - 24:00)" value="{{ $item['harga_malam'] ?? '' }}">
                                    <input class="form-control mb-2" name="diskon_persen" value="{{ $item['diskon_persen'] }}">
                                    <textarea class="form-control mb-2" name="deskripsi">{{ $item['deskripsi'] }}</textarea>

                                    <button class="btn btn-success">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL CREATE --}}
    <div class="modal fade" id="createModal">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('lapangan.store') }}">
                @csrf

                <div class="modal-content p-3">
                    <h5>Tambah Lapangan</h5>

                    <input class="form-control mb-2" name="nama_lapangan" placeholder="Nama">
                    <input class="form-control mb-2" name="harga_per_jam" placeholder="Harga">
                    <input class="form-control mb-2" name="diskon_persen" placeholder="Diskon">
                    <textarea class="form-control mb-2" name="deskripsi" placeholder="Deskripsi"></textarea>

                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection