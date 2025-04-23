<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container {
            width: 100% !important;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .form-select:focus, .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .image-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .char-count {
            font-size: 0.9rem;
            color: gray;
            text-align: right;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Add New Product</h4>
                <a href="{{ route('dashboard.products') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
    <label for="brand_id" class="form-label fw-bold">Brands</label>
    <select class="form-select select2" id="brand_id" name="brand_id" required>
        <option value="" disabled selected>Select Brand</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}">{{ $brand->name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label for="category_id" class="form-label fw-bold">Categories</label>
    <select class="form-select select2" id="category_id" name="category_id" required>
        <option value="" disabled selected>Select Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
        @endforeach
    </select>
</div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-bold">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sale_price" class="form-label fw-bold">Sale Price :</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" min="0">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label fw-bold">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="images" class="form-label fw-bold">Product Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" required>
                            <div class="image-preview"></div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            <p class="char-count">0 / 500 characters</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true
            });

            // Live Character Count for Description
            $("#description").on("input", function () {
                let count = $(this).val().length;
                $(".char-count").text(count + " / 500 characters");
            });

            // Preview Uploaded Images
            $("#images").on("change", function () {
                $(".image-preview").html("");
                Array.from(this.files).forEach(file => {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        $(".image-preview").append(`<img src="${e.target.result}" alt="preview">`);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
</body>
</html>
