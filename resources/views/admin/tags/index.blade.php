@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-2 mt-2">Tag List</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="GET" class="d-flex align-items-center">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name"
                    class="form-control me-2" style="width: 250px;">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <a href="{{ route('admin.tags.create') }}" class="btn btn-success">Create Tag</a>
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