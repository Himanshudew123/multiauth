@extends('layouts.app')

@section('title', 'Update Customer')

@section('content')
<div class="container mt-2">
    <h2 class="mb-3">Update Customer</h2>

    <form id="customerForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="row g-3">
            <!-- Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Name:</label>
                <input value="{{ $customer->name }}" type="text" name="name" class="form-control" id="name">
                <div id="nameError" class="text-danger small d-none">Name is required.</div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email:</label>
                <input value="{{ $customer->email }}" type="email" name="email" class="form-control" id="email">
                <div id="emailError" class="text-danger small d-none">Valid email is required.</div>
            </div>

            <!-- Phone -->
            <div class="col-md-6">
                <label for="number" class="form-label">Phone Number:</label>
                <input maxlength="10" value="{{ $customer->number }}" type="text" name="number" class="form-control" id="number">
                <div id="phoneError" class="text-danger small d-none">Phone number is required.</div>
            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender:</label>
                <select name="gender" class="form-select" id="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ $customer->gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $customer->gender === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
                <div id="genderError" class="text-danger small d-none">Gender is required.</div>
            </div>

            <!-- Password -->
            <div class="col-md-6">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Leave blank to keep current password">
                <div id="passwordError" class="text-danger small d-none">Password must be at least 6 characters.</div>
            </div>

            <!-- Bio -->
            <div class="col-md-6">
                <label for="bio" class="form-label">Bio:</label>
                <textarea name="bio" class="form-control" id="bio" rows="1">{{ $customer->bio }}</textarea>
            </div>

            <!-- Photo -->
            <div class="col-12">
                <label for="photo" class="form-label">Photo (Max 1MB):</label>
                <div class="input-group">
                    <input type="file" name="photo" class="form-control" id="photoInput" accept="image/*">
                    <button type="button" class="btn btn-outline-danger" id="removePhotoBtn" style="display:none;" onclick="removePhoto()">Remove</button>

                    @if($customer->photo)
                        <input type="hidden" name="remove_existing_photo" id="removeExistingPhoto" value="0">
                        <button type="button" class="btn btn-outline-warning ms-2" id="removeExistingPhotoBtn" onclick="markRemoveExistingPhoto()">Remove Existing</button>
                        <button type="button" class="btn btn-outline-primary ms-2" id="previewPhotoBtn" onclick="previewPhoto('{{ asset('storage/' . $customer->photo) }}')">Preview</button>
                    @endif
                </div>
                <div id="photoError" class="text-danger small mt-1 d-none">File size must be less than 1MB.</div>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex flex-wrap justify-content-center gap-2 mt-3 mb-3">
                <button type="reset" class="btn btn-secondary" onclick="clearErrors(); removePhoto();">Reset</button>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </form>
</div>

<!-- Modal for Image Preview -->
<div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-labelledby="photoPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="photoPreviewImage" class="img-fluid rounded shadow" src="" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const SECRET_KEY = '{{ config("app.aes_key") }}';

    function encryptData(data, key = SECRET_KEY) {
        return CryptoJS.AES.encrypt(JSON.stringify(data), key).toString();
    }

    function validateForm() {
        let valid = true;
        const requiredFields = [
            { id: 'name', error: 'nameError' },
            { id: 'email', error: 'emailError' },
            { id: 'number', error: 'phoneError' },
            { id: 'gender', error: 'genderError' }
        ];

        requiredFields.forEach(({ id, error }) => {
            const val = $(`#${id}`).val().trim();
            if (!val) {
                $(`#${error}`).removeClass('d-none');
                valid = false;
            } else {
                $(`#${error}`).addClass('d-none');
            }
        });

        const password = $('#password').val().trim();
        if (password && password.length < 6) {
            $('#passwordError').removeClass('d-none');
            valid = false;
        } else {
            $('#passwordError').addClass('d-none');
        }

        return valid;
    }

    function clearErrors() {
        $('.text-danger').addClass('d-none');
    }

    function previewPhoto(url) {
        $('#photoPreviewImage').attr('src', url);
        new bootstrap.Modal(document.getElementById('photoPreviewModal')).show();
    }

    function removePhoto() {
        $('#photoInput').val('');
        $('#removePhotoBtn, #previewPhotoBtn, #removeExistingPhotoBtn').hide();
        $('#removeExistingPhoto').val("1");
    }

    function markRemoveExistingPhoto() {
        $('#removeExistingPhoto').val("1");
        $('#removeExistingPhotoBtn, #previewPhotoBtn').hide();
        Swal.fire('Marked', 'Photo will be removed after update.', 'info');
    }

    $('#photoInput').on('change', function () {
        const file = this.files[0];
        if (file && file.size > 1024 * 1024) {
            $('#photoError').removeClass('d-none');
            $(this).val('');
            $('#removePhotoBtn').hide();
        } else {
            $('#photoError').addClass('d-none');
            if (file) $('#removePhotoBtn').show();
        }
    });

    $('#customerForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();
        if (!validateForm()) return;

        const form = this;
        const formData = new FormData(form);
        const plainData = {};

        formData.forEach((value, key) => {
            if (!['photo', '_token', '_method'].includes(key)) {
                plainData[key] = value;
            }
        });

        const encryptedPayload = encryptData(plainData);
        const finalFormData = new FormData();
        finalFormData.append('payload', encryptedPayload);
        finalFormData.append('_token', formData.get('_token'));
        finalFormData.append('_method', 'PATCH');

        const photo = $('#photoInput')[0].files[0];
        if (photo) finalFormData.append('photo', photo);

        if ($('#removeExistingPhoto').val() === '1') {
            finalFormData.append('remove_existing_photo', '1');
        }

        $.ajax({
            url: "{{ route('admin.customers.update', $customer->uuid) }}",
            method: 'POST',
            data: finalFormData,
            contentType: false,
            processData: false,
            success: function (res) {
                Swal.fire('Success', res.message || 'Customer updated.', 'success')
                    .then(() => window.location.href = "{{ route('admin.customers.index') }}");
            },
            error: function (xhr) {
                let errorMessages = [];
                const errors = xhr.responseJSON.field_errors;
                
                for (let field in errors) {
                    const errorId = `#${field}Error`;
                    if ($(errorId).length) {
                        $(errorId).text(errors[field][0]).removeClass('d-none');
                        errorMessages.push(`${field}: ${errors[field][0]}`);
                    }
                }
                
                if (errorMessages.length) {
                    Swal.fire('Validation Error', errorMessages.join('<br>'), 'warning');
                } else {
                    Swal.fire('Validation Error', 'Please correct the highlighted fields.', 'warning');
                }
            }
        });
    });
</script>
@endsection
