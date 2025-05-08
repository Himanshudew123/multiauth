@extends('layouts.app')

@section('content')
<title>{{ $pageTitle }}</title>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-1">
            <h2 class="">Category List</h2>
            <div>
            <a href="{{ route('admin.categories.export.csv') }}" class="btn btn-outline-info"><i class="fa-solid fa-file-csv"></i> Excel</a>
            <a href="{{ route('admin.categories.pdf')}}" target="_blank" class="mx-2 btn btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i>PDF</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="POST" action="{{ route('admin.categories.search') }}" class="row g-2 align-items-end">
                @csrf

                <div class="col-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', request('name')) }}"
                        placeholder="Search by name (3+ letters)" class="form-control">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                        class="form-control">
                    @error('start_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control">
                    @error('end_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success mt-4"><i class="fa-solid fa-plus"></i>
                Create
                Category</a>
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
                            <a href="{{ route('admin.categories.edit', $category->uuid) }}"
                                class="btn btn-sm btn-warning me-2">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category->uuid) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
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