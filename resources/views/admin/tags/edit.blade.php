@extends('layouts.app')

@section('content')
<title>{{ $pageTitle }}</title>
<div class="container">
    <h2 class="mb-4 mt-3">Edit Tag</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="editTagForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Tag Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $tag->name }}" required>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Update Tag</button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#editTagForm').submit(function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.tags.update', $tag->uuid) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Updated', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.tags.index') }}";
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Something went wrong.', 'error');
            }
        });
    });
</script>
@endsection
