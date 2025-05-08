@extends('layouts.app')

@section('title', 'Product List')

@section('content')
<title>{{ $pageTitle }}</title>
    <div class="container mt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="">Product List</h2>
            <div>
            <a href="{{ route('admin.products.export.csv') }}" class="btn btn-outline-info"><i class="fa-solid fa-file-csv"></i> Excel</a>
            <a href="{{ route('admin.products.pdf')}}" target="_blank" class="mx-2 btn btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i>PDF</a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="POST" action="{{ route('admin.products.index') }}" class="row g-3 align-items-end mb-4">
            @csrf
            <div class="col-md-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                    placeholder="Search by name">
            </div>
            <div class="col-md-3">
                <label for="price_min" class="form-label">Minimum Price</label>
                <input type="number" name="price_min" value="{{ request('price_min') }}" class="form-control"
                    placeholder="Min price">
            </div>
            <div class="col-md-3">
                <label for="price_max" class="form-label">Max Price</label>
                <input type="number" name="price_max" value="{{ request('price_max') }}" class="form-control"
                    placeholder="Max price">
            </div>
            <div class="col-md-3">
                <label for="created_at_start" class="form-label">Start Date</label>
                <input type="date" name="created_at_start" value="{{ request('created_at_start') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="created_at_end" class="form-label">End Date</label>
                <input type="date" name="created_at_end" value="{{ request('created_at_end') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">-- All Categories --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                    <i class="fa-solid fa-plus"></i> Create New Product
                </a>
            </div>
        </form>

        <!-- Summary -->


        <!-- Product Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th class="text-right">Price</th>
                        <th>Category</th>
                        <th>Tags</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td>{{ $product->name }}</td>
                            <td class="text-right">₹{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>
                                @foreach ($product->tags as $tag)
                                    <span class="badge bg-info text-dark">{{ $tag->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $product->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.products.show', $product->uuid) }}"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('admin.products.edit', $product->uuid) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product->uuid) }}" method="POST"
                                    style="display:inline-block;"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        <div class="d-flex justify-content-between align-items-center">
            <div class="col-md-6 text-start">
                <strong>
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                </strong>
            </div>
            <nav class="d-flex justify-content-end">
                {{ $products->withQueryString()->links('pagination::bootstrap-4') }}
            </nav>
        </div>

        <!-- Pagination -->

    </div>
@endsection

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session("success") }}',
                confirmButtonColor: '#3085d6'
            })
        });
    </script>
@endif