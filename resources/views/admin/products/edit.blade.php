@extends('layouts.app')

@section('title', 'Update Product')

@section('content')
<div class="container mt-2">
    <h2 class="mb-3">Update Product</h2>

    <form id="productForm" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Product Name:</label>
                <input value="{{ $product->name }}" type="text" name="name" class="form-control" id="name">
                <div id="nameError" class="text-danger small d-none">Name is required.</div>
            </div>

            <!-- Price -->
            <div class="col-md-6">
                <label for="price" class="form-label">Price (₹):</label>
                <input value="{{ $product->price }}" type="number" step="0.01" name="price" class="form-control" id="price">
                <div id="priceError" class="text-danger small d-none">Price is required.</div>
            </div>

            <!-- Category -->
            <div class="col-md-6">
                <label for="category_id" class="form-label">Category:</label>
                <select name="category_id" class="form-select" id="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <div id="category_idError" class="text-danger small d-none">Category is required.</div>
            </div>

            <!-- Tags -->
            <div class="col-md-6">
                <label for="tags" class="form-label">Tags:</label>
                <select name="tags[]" id="tags" class="form-select" multiple="multiple">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" @if(in_array($tag->id, $product->tags->pluck('id')->toArray())) selected @endif>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
                <div id="tagsError" class="text-danger small d-none">At least one tag is required.</div>
            </div>

            <!-- Photo -->
            <div class="col-12">
                <label for="photo" class="form-label">Product Photo (Max 1MB):</label>
                <div class="input-group">
                    <input type="file" name="photo" class="form-control" id="photoInput" accept="image/*">
                    <button type="button" class="btn btn-outline-danger" id="removePhotoBtn" style="display:none;" onclick="removePhoto()">Remove</button>

                    @php
                        $existingPhoto = $product->photo ? json_decode($product->photo, true)[0] ?? null : null;
                    @endphp

                    @if($existingPhoto)
                        <input type="hidden" name="remove_existing_photo" id="removeExistingPhoto" value="0">
                        <button type="button" class="btn btn-outline-warning ms-2" id="removeExistingPhotoBtn" onclick="markRemoveExistingPhoto()">Remove Existing</button>
                        <button type="button" class="btn btn-outline-primary ms-2" id="previewPhotoBtn" onclick="previewPhoto('{{ asset('storage/' . $existingPhoto) }}')">Preview</button>
                    @endif
                </div>
                <div id="photoError" class="text-danger small mt-1 d-none">File size must be less than 1MB.</div>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex flex-wrap justify-content-center gap-2 mt-3 mb-3">
                <button type="reset" class="btn btn-secondary" onclick="clearErrors(); removePhoto();">Reset</button>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to Products</a>
            </div>
        </div>
    </form>
</div>

<!-- Modal for Preview -->
<div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Photo</h5>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    const SECRET_KEY = '{{ config("app.aes_key") }}';

    function encryptData(data, key = SECRET_KEY) {
        return CryptoJS.AES.encrypt(JSON.stringify(data), key).toString();
    }

    function validateForm() {
        let valid = true;
        const fields = [
            { id: 'name', error: 'nameError' },
            { id: 'price', error: 'priceError' },
            { id: 'category_id', error: 'category_idError' },
            { id: 'tags', error: 'tagsError' }
        ];

        fields.forEach(({ id, error }) => {
            const value = $(`#${id}`).val();
            if (!value || (Array.isArray(value) && !value.length)) {
                $(`#${error}`).removeClass('d-none');
                valid = false;
            } else {
                $(`#${error}`).addClass('d-none');
            }
        });

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

    $('#tags').select2({
        placeholder: "Select Tags",
        allowClear: true
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#productForm').on('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) return;

        const formData = new FormData();
        const data = {
            name: $('#name').val().trim(),
            price: $('#price').val().trim(),
            category_id: $('#category_id').val(),
            tags: $('#tags').val()
        };

        const encrypted = encryptData(data);
        formData.append('payload', encrypted);
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('_method', 'PUT'); // IMPORTANT for Laravel PUT spoofing

        const file = $('#photoInput')[0].files[0];
        if (file) {
            formData.append('photo', file);
        }

        if ($('#removeExistingPhoto').length) {
            formData.append('remove_existing_photo', $('#removeExistingPhoto').val());
        }

        $.ajax({
            url: "{{ route('admin.products.update', $product->uuid) }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
    Swal.fire({
        title: 'Updated!',
        text: res.message,
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = "{{ route('admin.products.index') }}";
    });
},
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let msg = '';
                    for (const key in xhr.responseJSON.errors) {
                        msg += `${xhr.responseJSON.errors[key].join(', ')}\n`;
                    }
                    Swal.fire('Validation Error', msg, 'error');
                } else {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            }
        });
    });
</script>
@endsection
