@extends('layouts.app')

@section('title', 'User List')

@section('content')
<title>{{ $pageTitle }}</title>
    <div class="container mt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="">User List</h2>
            <div>
            <a href="{{ route('admin.customers.export.csv') }}" class="btn btn-outline-info"><i class="fa-solid fa-file-csv"></i> Excel</a>
            <a href="{{ route('admin.customers.pdf')}}" target="_blank" class="mx-2 btn btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i>PDF</a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="POST" action="{{ route('admin.customers.index') }}" class="row g-3 align-items-end mb-4">
            @csrf
            <!-- Name -->
            <div class="col-md-2">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" value="{{ request('name') }}" class="form-control"
                    placeholder="Search by name">
            </div>

            <!-- Phone Number -->
            <div class="col-md-2">
                <label for="number" class="form-label">Phone Number</label>
                <input type="number" id="number" name="number" value="{{ request('number') }}" class="form-control"
                    pattern="[0-9]*" inputmode="numeric" placeholder="Search by number">
            </div>

            <!-- Date Range -->
            <div class="col-md-4">

                <div class="row g-2">
                    <div class="col">
                        <label for="" class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>

                    <div class="col">
                        <label for="" class="form-label">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary w-100">Reset</a>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-success w-100">Create</a>

            </div>
        </form>





        <!-- Table -->
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
                            <td>
                                @php
                                    $genderMap = [1 => 'Male', 2 => 'Female', 3 => 'Other'];
                                @endphp
                                {{ $genderMap[$customer->gender] ?? 'N/A' }}
                            </td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer->uuid) }}"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('admin.customers.edit', $customer->uuid) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.customers.destroy', $customer->uuid) }}" method="POST"
                                    style="display:inline-block;"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
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





        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-4">
                <strong>Showing {{ $customers->firstItem() }} - {{ $customers->lastItem() }} of {{ $customers->total() }}
                    users</strong>
            </div>
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