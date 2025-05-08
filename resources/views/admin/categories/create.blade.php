@extends('layouts.app')

@section('content')
<title>{{ $pageTitle }}</title>
<div class="container mt-3">
    <h2 class="mb-4">Create New Category</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="createCategoryForm" method="POST">
                @csrf <!-- Laravel CSRF token -->
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" required>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Create Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<script>
    const SECRET_KEY = '{{ config("app.aes_key") }}'; // Ensure this is set in your .env

    function encryptObject(data, key) {
        const jsonString = JSON.stringify(data);
        return CryptoJS.AES.encrypt(jsonString, key).toString();
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
        }
    });

    $('#createCategoryForm').submit(function(e) {
        e.preventDefault();

        const submitButton = $('button[type="submit"]');
        submitButton.prop('disabled', true).text('Creating...');

        const plainData = {
            name: $('#name').val()
        };

        const encryptedPayload = encryptObject(plainData, SECRET_KEY);

        const formData = new FormData();
        formData.append('payload', encryptedPayload);

        $.ajax({
            url: "{{ route('admin.categories.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Created!',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "{{ route('admin.categories.index') }}";
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
                submitButton.prop('disabled', false).text('Create Category');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                submitButton.prop('disabled', false).text('Create Category');
            }
        });
    });
</script>
@endsection
