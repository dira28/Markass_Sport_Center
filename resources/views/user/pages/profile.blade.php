@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="profile-page py-5">
    <div class="container">
        <div class="profile-card text-center">

            {{-- AVATAR --}}
            <div class="avatar-circle-lg">
                {{ strtoupper(substr(session('user.nama') ?? 'U', 0, 1)) }}
            </div>

            {{-- NAMA --}}
            <h4 class="mt-3 mb-1">
                {{ session('user.nama') ?? '-' }}
            </h4>



            {{-- EMAIL --}}
            <p class="text-muted">
                {{ session('user.email') ?? '-' }}
            </p>

            <div class="profile-divider my-3"></div>

            {{-- INFO --}}
            <div class="profile-info text-start">

                <div class="mb-2">
                    <small>Nama Lengkap</small>
                    <p class="mb-0 fw-semibold">
                        {{ session('user.nama') ?? '-' }}
                    </p>
                </div>



                <div class="mb-3">
                    <small>Email</small>
                    <p class="mb-0 fw-semibold">
                        {{ session('user.email') ?? '-' }}
                    </p>
                </div>

            </div>

            {{-- LOGOUT --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-danger text-white fw-semibold w-100">
                    Logout
                </button>
            </form>

        </div>
    </div>
</div>
@endsection
