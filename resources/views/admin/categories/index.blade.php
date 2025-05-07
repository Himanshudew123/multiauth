@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3 mt-2">Category List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <form method="POST" class="d-flex align-items-center">
            @csrf
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name" class="form-control me-2" style="width: 250px;">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary mx-2">Reset</a>
        </form>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Create Category</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Created At</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $index => $category)
                <tr>
                    <td>{{ $categories->firstItem() + $index }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.categories.edit', $category->uuid) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category->uuid) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center justify-content-center mb-4">
                <strong>Showing {{ $categories->firstItem() }} - {{ $categories->lastItem() }} of {{ $categories->total() }}
                Categories</strong>
            </div>
            <nav>
                {{ $categories->withQueryString()->links('pagination::bootstrap-4') }}
            </nav>
        </div>
</div>

{{-- Optional custom style --}}
@push('styles')
<style>
    .pagination .page-item .page-link {
        border-radius: 30px !important;
        margin: 0 4px;
        padding: 6px 14px;
        color: #0d6efd;
        border-color: #dee2e6;
        transition: background-color 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
    }
</style>
@endpush
@endsection
