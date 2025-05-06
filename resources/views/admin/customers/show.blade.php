@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">

                <!-- Header -->
                <div class="text-center mb-2">
                    <h2 class="fw-bold text-primary">Customer Profile</h2>
                    <p class="text-muted">Complete details of the selected customer</p>
                </div>

                <!-- Profile Section (No card, just clean layout) -->
                <div class="row align-items-center mb-2">
                    <!-- Profile Photo -->
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        @if($customer->photo)
                        
                            <img src="{{ asset('storage/' . $customer->photo) }}" 
                                 alt="Profile Photo" 
                                 class="img-fluid rounded-circle shadow-sm"
                                 style="width: 200px; height: 200px; object-fit: cover; border: 4px solid #f0f0f0;">
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white"
                                 style="width: 200px; height: 200px;">
                                <span>No Photo</span>
                            </div>
                        @endif
                        <h5 class="mt-4 fw-semibold">{{ $customer->name }}</h5>
                       
                    </div>

                    <!-- Customer Info -->
                    <div class="col-md-8">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">Email</h6>
                                <p class="text-muted mb-0">{{ $customer->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">Phone Number</h6>
                                <p class="text-muted mb-0">{{ $customer->number }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">Bio</h6>
                                <p class="text-muted mb-0">{{ $customer->bio }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">Gender</h6>
                                <p class="text-muted mb-0">{{ $customer->gender }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mb-3">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary px-4 py-2 rounded-pill">
                        ← Back to Customer List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
