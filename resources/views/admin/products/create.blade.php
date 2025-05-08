@extends('layouts.app')

@section('content')
<title>{{ $pageTitle }}</title>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    body {
        background: linear-gradient(135deg, #dfe9f3, #ffffff);
        font-family: 'Segoe UI', sans-serif;
    }



    .form-label {
        font-weight: 500;
    }

    .select2-container--bootstrap-5 .select2-selection--single,
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 45px;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
    }

    .select2-selection__choice {
        background-color: #0d6efd !important;
        color: #fff !important;
        border: none !important;
        padding: 0.25rem 0.5rem !important;
        border-radius: 0.4rem !important;
        font-size: 0.875rem;
    }
</style>

<div class="container">
  

    
        <h3 class="mb-3 text-primary mt-2">Add New Product</h3>

        <form id="productForm" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="price" class="form-label">Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label for="images" class="form-label">Upload Images</label>
                    <input type="file" name="images[]" id="images" class="form-control" multiple>
                </div>

                <div class="col-md-12">
                    <label for="tags" class="form-label">Tags</label>
                    <select name="tags[]" id="tags" class="form-select tags" multiple="multiple" required>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-3 mb-2 text-end">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mx-2">Back</a>
            <button type="reset" class="btn btn-primary mx-2 ">Reset</button>
                <button type="submit" class="btn btn-success mx-2 ">Add Product</button>
               
            </div>
        </form>
  
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<script>
    const SECRET_KEY = '{{ config("app.aes_key") }}';

    function encryptData(data, key = SECRET_KEY) {
        const jsonData = JSON.stringify(data);
        return CryptoJS.AES.encrypt(jsonData, key).toString();
    }

    $(document).ready(function () {
        $('#tags').select2({
            theme: 'bootstrap-5',
            placeholder: "Select tags",
            allowClear: true
        });

        $('#productForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData();

            let plainData = {
                name: $('#name').val(),
                price: $('#price').val(),
                category_id: $('#category_id').val(),
                tags: $('#tags').val()
            };

            const encryptedPayload = encryptData(plainData);
            formData.append('payload', encryptedPayload);
            formData.append('_token', $('input[name="_token"]').val());

            // Add files
            let files = $('#images')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }
            console.log(formData);
            
            
            $.ajax({
                url: "{{ route('admin.products.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    alert(response.message || 'Product created successfully!');
                    window.location.href = "{{ route('admin.products.index') }}";
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message;
                    if (Array.isArray(msg)) {
                        alert(msg.join("\n"));
                    } else {
                        alert(msg || "An unexpected error occurred.");
                    }
                }
            });
        });
    });
</script>
@endsection
