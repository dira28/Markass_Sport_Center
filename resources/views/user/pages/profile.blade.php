@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-md-7">

                <!-- Profile Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    <!-- Header Gradient -->
                    <div class="bg-primary text-white text-center py-5">
                        <div class="mb-3">
                            <!-- Avatar -->
                            <div
                                style="width:90px;height:90px;margin:auto;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:bold;color:#0d6efd;">
                                {{ strtoupper(substr($user['nama'], 0, 1)) }}
                            </div>
                        </div>

                        <h4 class="mb-0">{{ $user['nama'] }}</h4>
                        <small>{{ $user['email'] }}</small>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">

                        <div class="mb-3 p-3 bg-light rounded-3">
                            <small class="text-muted">Nama Lengkap</small>
                            <div class="fw-semibold">{{ $user['nama'] }}</div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded-3">
                            <small class="text-muted">Email</small>
                            <div class="fw-semibold">{{ $user['email'] }}</div>
                        </div>

                        <a href="{{ route('logout') }}" class="btn btn-danger w-100 rounded-3">
                            Logout
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection