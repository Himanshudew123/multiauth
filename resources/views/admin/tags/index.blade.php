@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-1">
            <h2 class="">Tag List</h2>
            <a href="{{ route('admin.tags.export.csv') }}" class="btn btn-outline-info"><i class="fa-solid fa-file-csv"></i> Download</a>

        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="POST" action="{{ route('admin.tags.index') }}" class="row g-2 align-items-end">
                @csrf

                <div class="col-auto">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', request('name')) }}"
                        placeholder="Search by name (3+ letters)" class="form-control">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-auto">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                        class="form-control">
                    @error('start_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-auto">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control">
                    @error('end_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
            <a href="{{ route('admin.tags.create') }}" class="btn btn-success mt-4"><i class="fa-solid fa-plus"></i> Create
                Tag</a>
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
                @forelse($tags as $index => $tag)
                    <tr>
                        <td>{{ $tags->firstItem() + $index }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>{{ $tag->created_at->format('d M Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.tags.edit', $tag->uuid) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                            <form action="{{ route('admin.tags.destroy', $tag->uuid) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this tag?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No tags found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center justify-content-center mb-4">
                <strong>Showing {{ $tags->firstItem() }} - {{ $tags->lastItem() }} of {{ $tags->total() }}
                    Tags</strong>
            </div>
            <nav>
                {{ $tags->withQueryString()->links('pagination::bootstrap-4') }}
            </nav>
        </div>



    </div>
@endsection