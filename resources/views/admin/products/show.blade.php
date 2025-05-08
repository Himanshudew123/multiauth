@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<title>{{ $pageTitle }}</title>
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">

                <!-- Header -->
                <div class="text-center mb-2">
                    <h2 class="fw-bold text-primary">Product Details</h2>
                    <p class="text-muted">Complete details of the selected product</p>
                </div>

                <!-- Product Section -->
                <div class="row align-items-center mb-2">
                    <!-- Product Photos -->
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                      
                        @if($product->photo && is_array(json_decode($product->photo)))
                            <div id="product-images">
                                @foreach(json_decode($product->photo) as $image)
                                    <!-- Display each image from the JSON array -->
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Product Image" 
                                         class="img-fluid rounded shadow-sm mb-2"
                                         style="width: 200px; height: 200px; object-fit: cover; border: 4px solid #f0f0f0;">
                                @endforeach
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded bg-secondary text-white"
                                 style="width: 200px; height: 200px;">
                                <span>No Image</span>
                            </div>
                        @endif
                        <h5 class="mt-4 fw-semibold">{{ $product->name }}</h5>
                    </div>

                    <!-- Product Info -->
                    <div class="col-md-8">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">Price</h6>
                                <p class="text-muted mb-0">₹{{ number_format($product->price, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">Category</h6>
                                <p class="text-muted mb-0">{{ $product->category->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-12">
                                <h6 class="fw-bold mb-1">Tags</h6>
                                @if($product->tags->isNotEmpty())
                                    <p class="text-muted mb-0">
                                        @foreach($product->tags as $tag)
                                            <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                                        @endforeach
                                    </p>
                                @else
                                    <p class="text-muted mb-0">No Tags</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mb-3">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary px-4 py-2 rounded-pill">
                        ← Back to Product List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
