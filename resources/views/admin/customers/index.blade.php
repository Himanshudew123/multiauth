@extends('layouts.app')

@section('title', 'User List')

@section('content')
    <div class="container mt-2">
        <h2 class="mb-4">User List</h2>

        <!-- Filter Form -->
        <form method="POST" action="{{ route('admin.customers.index') }}" class="row g-3 align-items-end mb-4">
            @csrf
            <div class="col-md-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                    placeholder="Search by name">
            </div>
            <div class="col-md-3">
                <label for="number" class="form-label">Phone Number</label>
                <input type="text" name="number" value="{{ request('number') }}" class="form-control"
                    placeholder="Search by number">
            </div>
            <div class="col-md-2">
                <label for="created_at" class="form-label">Created Date</label>
                <input type="date" name="created_at" value="{{ request('created_at') }}" class="form-control">
            </div>
            <div class="col-md-4 d-flex gap-3">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Create New User</a>
            </div>
        </form>

        <!-- Create Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
           
            <div>
                <strong>Showing {{ $customers->firstItem() }} - {{ $customers->lastItem() }} of {{ $customers->total() }}
                    users</strong>
            </div>
        </div>

        <!-- User Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Serial No</th>
                       
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $index }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->number }}</td>
                            <td>{{ $customer->gender }}</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer->uuid) }}"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('admin.customers.edit', $customer->uuid) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.customers.destroy', $customer->uuid) }}" method="POST"
                                    style="display:inline-block;" onclick="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            
            <nav>
                {{ $customers->withQueryString()->links('pagination::bootstrap-4') }}
            </nav>
        </div>
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
