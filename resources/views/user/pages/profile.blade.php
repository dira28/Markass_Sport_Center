@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
<style>
    .btn-logout-markas {
        transition: all 0.2s ease-in-out !important;
    }
    .btn-logout-markas:hover {
        background-color: #bb2d3b !important;
        transform: translateY(-1px) !important;
        cursor: pointer !important;
        box-shadow: 0 6px 16px rgba(187,45,59,0.5) !important;
    }
</style>
@endpush

@section('content')
    <div class="profile-page py-5 bg-light min-vh-100 flex-grow-1">
        <div class="container d-flex justify-content-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center" style="max-width: 420px; width: 100%;">

                {{-- AVATAR GOOGLE STYLE --}}
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-danger text-white shadow-sm font-monospace fw-bold"
                    style="width: 80px; height: 80px; font-size: 2.2rem;">
                    {{ strtoupper(substr(session('user.nama') ?? 'U', 0, 1)) }}
                </div>

                {{-- NAMA UTAMA --}}
                <h5 class="mb-1 fw-bold text-dark text-break">
                    {{ session('user.nama') ?? 'User Markas' }}
                </h5>

                {{-- EMAIL UTAMA --}}
                <p class="text-muted small text-break mb-3">
                    {{ session('user.email') ?? '-' }}
                </p>

                <hr class="text-muted opacity-25 my-3">

                {{-- DETAIL INFO GRID --}}
                <div class="text-start bg-light p-3 rounded-3 mb-3 border border-light-subtle">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <small class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">Nama Lengkap</small>
                            <p class="mb-0 fw-semibold text-dark text-break">
                                {{ session('user.nama') ?? '-' }}
                            </p>
                        </div>

                        <div class="col-12">
                            <small class="text-secondary text-uppercase fw-semibold"
                                style="font-size: 0.75rem;">Email</small>
                            <p class="mb-0 fw-semibold text-dark text-break">
                                {{ session('user.email') ?? '-' }}
                            </p>
                        </div>
                </div>

                {{-- LOGOUT BUTTON --}}
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit"
                        style="background-color: #dc3545 !important; color: #ffffff !important; font-weight: bold !important; border-radius: 8px !important; box-shadow: 0 4px 12px rgba(220,53,69,0.4) !important; border: none !important;"
                        class="btn btn-lg w-100 py-2 btn-logout-markas">
                        LOGOUT
                    </button>
                </form>

            </div>
    </div>
@endsection
