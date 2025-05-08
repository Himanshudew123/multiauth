@extends('layouts.app')

@section('content')
<title>{{ $pageTitle }}</title>
<div class="container mt-3">
    <h2 class="mb-4">Create New Tag</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="createTagForm" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Tag Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter tag name" required>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Create Tag</button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Cancel</a>
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
    const SECRET_KEY = '{{ config("app.aes_key") }}';

    function encryptObject(data, key) {
        const jsonString = JSON.stringify(data);
        return CryptoJS.AES.encrypt(jsonString, key).toString();
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
        }
    });
    $('#createTagForm').submit(function(e) {
        e.preventDefault();

        const button = $('button[type="submit"]');
        button.prop('disabled', true).text('Creating...');

        const plainData = { name: $('#name').val() };
        const encryptedPayload = encryptObject(plainData, SECRET_KEY);

        const formData = new FormData();
        formData.append('payload', encryptedPayload);

        $.ajax({
            url: "{{ route('admin.tags.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.tags.index') }}";
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
                button.prop('disabled', false).text('Create Tag');
            },
            error: function() {
                Swal.fire('Error', 'Something went wrong.', 'error');
                button.prop('disabled', false).text('Create Tag');
            }
        });
    });
</script>
@endsection
