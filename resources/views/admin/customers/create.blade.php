@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')
<div class="container mt-2">
    <h2 class="mb-3">Create New Customer</h2>

    <form id="customerForm" action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <!-- Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" class="form-control" id="name">
                <div id="nameError" class="text-danger small d-none">Name is required.</div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" id="email">
                <div id="emailError" class="text-danger small d-none">Valid email is required.</div>
            </div>

            <!-- Password -->
            <div class="col-md-6">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" id="password">
                <div id="passwordError" class="text-danger small d-none">Password is required.</div>
            </div>

            <!-- Phone Number -->
            <div class="col-md-6">
                <label for="number" class="form-label">Phone Number:</label>
                <input type="text" name="number" class="form-control" id="number">
                <div id="phoneError" class="text-danger small d-none">Phone number is required.</div>
            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender:</label>
                <select name="gender" class="form-select" id="gender">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <div id="genderError" class="text-danger small d-none">Gender is required.</div>
            </div>

            <!-- Bio -->
            <div class="col-md-6">
                <label for="bio" class="form-label">Bio:</label>
                <textarea name="bio" class="form-control" id="bio" rows="1"></textarea>
            </div>

            <!-- Photo -->
            <div class="col-12">
                <label for="photo" class="form-label">Photo (Max 1MB):</label>
                <div class="input-group">
                    <input type="file" name="photo" class="form-control" id="photoInput" accept="image/*">
                    <button type="button" class="btn btn-outline-danger" id="removePhotoBtn" style="display: none;" onclick="removePhoto()">Remove</button>
                </div>
                <div id="photoError" class="text-danger small mt-1 d-none">File size must be less than 1MB.</div>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex justify-content-center mt-3 mb-3">
                <button type="reset" class="btn btn-secondary me-2 col-1" onclick="clearErrors(); removePhoto();">Reset</button>
                <button type="submit" class="btn btn-primary col-1">Create</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mx-2 col-2">Back to home</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const SECRET_KEY = '{{ config("app.aes_key") }}'; // Set this in your .env file securely

function encryptData(data, key = SECRET_KEY) {
    const dataString = JSON.stringify(data);  // Ensure data is a string
    return CryptoJS.AES.encrypt(dataString, key).toString();
}


function validateForm() {
    let valid = true;

    const fields = [
        { id: 'name', error: 'nameError' },
        { id: 'email', error: 'emailError' },
        { id: 'password', error: 'passwordError' },
        { id: 'number', error: 'phoneError' },
        { id: 'gender', error: 'genderError' }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const showError = input.value.trim() === '';
        document.getElementById(field.error).classList.toggle('d-none', !showError);
        if (showError) valid = false;
    });

    return valid;
}

function clearErrors() {
    ['nameError', 'emailError', 'passwordError', 'phoneError', 'genderError', 'photoError']
        .forEach(id => document.getElementById(id).classList.add('d-none'));
}

function removePhoto() {
    const photo = document.getElementById('photoInput');
    const removeBtn = document.getElementById('removePhotoBtn');
    photo.value = "";
    removeBtn.style.display = "none";
}

document.getElementById('photoInput').addEventListener('change', function() {
    const file = this.files[0];
    const errorDiv = document.getElementById('photoError');
    const removeBtn = document.getElementById('removePhotoBtn');

    if (file && file.size > 1024 * 1024) {
        errorDiv.classList.remove('d-none');
        this.value = "";
        removeBtn.style.display = "none";
    } else {
        errorDiv.classList.add('d-none');
        if (file) removeBtn.style.display = "inline-block";
    }
});

document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    clearErrors();

    if (!validateForm()) return;

    const form = this;
    const formData = new FormData(form);
    const plainData = {};

    formData.forEach((value, key) => {
        if (key !== 'photo' && key !== '_token') plainData[key] = value;
    });

    // Encrypt the form data
    const encryptedData = encryptData(plainData);

    // Create FormData object to send the encrypted data along with the photo (if present)
    const ajaxFormData = new FormData();
    ajaxFormData.append('payload', encryptedData); // Send only the encrypted data
    ajaxFormData.append('_token', form.querySelector('input[name="_token"]').value);

    const photo = document.getElementById('photoInput');
    if (photo.files.length > 0) {
        ajaxFormData.append('photo', photo.files[0]);
    }
    console.log(ajaxFormData); // For debugging
    

    // Send the encrypted data using AJAX
    $.ajax({
        url:form.action,
        method: 'POST',
        data: ajaxFormData,
        contentType: false,
        processData: false,
        success: function(response) {
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
    })
});
</script>
@endsection
