@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Profile</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> {{ $user['nama'] }}</p>
                    <p><strong>Email:</strong> {{ $user['email'] }}</p>
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection