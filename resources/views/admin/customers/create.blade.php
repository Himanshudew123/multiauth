@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')
<title>{{ $pageTitle }}</title>
<div class="container mt-2">
    <h2 class="mb-3">Create New Customer</h2>

    <form id="customerForm" action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <!-- Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" class="form-control" id="name">
                <div id="nameError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" id="email">
                <div id="emailError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Password -->
            <div class="col-md-6">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" id="password">
                <div id="passwordError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Phone Number -->
            <div class="col-md-6">
                <label for="number" class="form-label">Phone Number:</label>
                <input maxlength="10" type="text" name="number" class="form-control" id="number">
                <div id="numberError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender:</label>
                <select name="gender" class="form-select" id="gender">
                    <option value="">Select Gender</option>
                    <option value="1">Male</option>
                    <option value="2">Female</option>
                    <option value="3">Other</option>
                </select>
                <div id="genderError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Bio -->
            <div class="col-md-6">
                <label for="bio" class="form-label">Bio:</label>
                <textarea name="bio" class="form-control" id="bio" rows="1"></textarea>
                <div id="bioError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Photo -->
            <div class="col-12">
                <label for="photo" class="form-label">Photo (Max 1MB):</label>
                <div class="input-group">
                    <input type="file" name="photo" class="form-control" id="photoInput" accept="image/*">
                    <button type="button" class="btn btn-outline-danger" id="removePhotoBtn" style="display: none;" onclick="removePhoto()">Remove</button>
                </div>
                <div id="photoError" class="text-danger small mt-1 d-none"></div>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex justify-content-center mt-3 mb-3">
                <button type="reset" class="btn btn-secondary me-2 col-1" onclick="clearErrors(); removePhoto();">Reset</button>
                <button type="submit" class="btn btn-primary col-1">Create</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mx-2 col-2">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const SECRET_KEY = '{{ config("app.aes_key") }}';

    function encryptData(data, key = SECRET_KEY) {
        return CryptoJS.AES.encrypt(JSON.stringify(data), key).toString();
    }

    function validateForm() {
        let valid = true;
        const fields = [
            { id: 'name', errorId: 'nameError' },
            { id: 'email', errorId: 'emailError' },
            { id: 'password', errorId: 'passwordError' },
            { id: 'number', errorId: 'numberError' },
            { id: 'gender', errorId: 'genderError' },
        ];

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            const error = document.getElementById(field.errorId);
            const value = input.value.trim();

            if (!value) {
                error.textContent = `${field.id.charAt(0).toUpperCase() + field.id.slice(1)} is required.`;
                error.classList.remove('d-none');
                valid = false;
            } else {
                error.textContent = '';
                error.classList.add('d-none');
            }
        });

        return valid;
    }

    function clearErrors() {
        document.querySelectorAll('.text-danger').forEach(el => {
            el.classList.add('d-none');
            el.textContent = '';
        });
    }

    window.removePhoto = function () {
        const photoInput = document.getElementById('photoInput');
        photoInput.value = "";
        document.getElementById('removePhotoBtn').style.display = "none";
        document.getElementById('photoError').classList.add('d-none');
    }

    document.getElementById('photoInput').addEventListener('change', function () {
        const file = this.files[0];
        const errorDiv = document.getElementById('photoError');
        const removeBtn = document.getElementById('removePhotoBtn');

        if (file && file.size > 1024 * 1024) {
            errorDiv.textContent = 'File size must be less than 1MB.';
            errorDiv.classList.remove('d-none');
            this.value = "";
            removeBtn.style.display = "none";
        } else {
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';
            if (file) removeBtn.style.display = "inline-block";
        }
    });

    document.getElementById('customerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        if (!validateForm()) return;

        const form = this;
        const formData = new FormData(form);
        const plainData = {};

        formData.forEach((value, key) => {
            if (key !== 'photo' && key !== '_token') {
                plainData[key] = value;
            }
        });

        const encryptedData = encryptData(plainData);
        const ajaxFormData = new FormData();
        ajaxFormData.append('payload', encryptedData);
        ajaxFormData.append('_token', '{{ csrf_token() }}');

        const photo = document.getElementById('photoInput');
        if (photo.files.length > 0) {
            ajaxFormData.append('photo', photo.files[0]);
        }

        $.ajax({
            url: form.action,
            method: 'POST',
            data: ajaxFormData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route("admin.customers.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (res) {
                clearErrors();
                const response = res.responseJSON;
                console.log(response["field_errors"]);
                
                if (response && response.field_errors) {
                    for (const key in response.field_errors) {
                        const message = response.field_errors[key][0];
                        const errorDiv = document.getElementById(key + 'Error');
                        if (errorDiv) {
                            errorDiv.textContent = message;
                            errorDiv.classList.remove('d-none');
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        text: 'Please fix the highlighted errors below.',
                        confirmButtonText: 'OK'
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: 'An unexpected error occurred.',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });
});
</script>
@endsection
