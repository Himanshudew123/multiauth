@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 mt-3">Edit Category</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#editCategoryForm').submit(function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.categories.update', $category->uuid) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Updated',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "{{ route('admin.categories.index') }}";
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            }
        });
    });
</script>
@endsection
