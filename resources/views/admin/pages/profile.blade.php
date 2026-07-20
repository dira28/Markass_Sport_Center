@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">

        <div class="profile-card text-center">

            {{-- AVATAR --}}
            <div class="avatar-circle-lg">
                {{ strtoupper(substr(session('user')['nama'] ?? 'A', 0, 1)) }}
            </div>

            {{-- NAMA --}}
            <h4 class="mt-3 mb-1">
                {{ session('user')['nama'] ?? 'Admin' }}
            </h4>

            {{-- EMAIL --}}
            <p class="text-muted">
                {{ session('user')['email'] ?? '-' }}
            </p>

            <div class="profile-divider my-3"></div>

            {{-- INFO --}}
            <div class="profile-info text-start">

                <div class="mb-2">
                    <small>Nama</small>
                    <p class="mb-0 fw-semibold">
                        {{ session('user')['nama'] ?? '-' }}
                    </p>
                </div>

                <div class="mb-3">
                    <small>Email</small>
                    <p class="mb-0 fw-semibold">
                        {{ session('user')['email'] ?? '-' }}
                    </p>
                </div>

            </div>

            {{-- LOGOUT --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100 mt-3">
                    Logout
                </button>
            </form>

        </div>

    </div>

@endsection