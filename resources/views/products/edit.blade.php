<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 750px;
        }
        .card {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .form-label {
            font-weight: 600;
        }
        .btn {
            padding: 10px 16px;
            font-size: 16px;
        }
        .img-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .img-preview img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
            object-fit: cover;
        }
        .form-select:focus, .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card p-4">
            <h2 class="text-center mb-4"><i class="fas fa-edit me-2"></i> Edit Product</h2>

            <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>

                <div class="mb-3">
    <label class="form-label">Brand</label>
    <select name="brand_id" class="form-select select2" required>
        @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}" {{ $selectedBrandId == $brand->brand_id ? 'selected' : '' }}>
                {{ $brand->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Category</label>
    <select name="category_id" class="form-select select2" required>
        @foreach($categories as $category)
            <option value="{{ $category->category_id }}" {{ $selectedCategoryId == $category->category_id ? 'selected' : '' }}>
                {{ $category->category_name }}
            </option>
        @endforeach
    </select>
</div>


                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sale Price</label>
                    <input type="number" name="sale_price" class="form-control" value="{{ $product->sale_price }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="available" {{ $product->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $product->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="{{ $product->quantity }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ $product->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Images</label>
                    <div class="img-preview">
                        @foreach(json_decode($product->images, true) as $image)
                            <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail">
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload New Images</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imageInput">
                    <div class="img-preview mt-2" id="previewContainer"></div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('dashboard.products') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: false
            });

            $('#imageInput').on('change', function(event) {
                let previewContainer = $('#previewContainer');
                previewContainer.html(""); // Clear existing previews

                let files = event.target.files;
                if (files.length > 0) {
                    for (let i = 0; i < files.length; i++) {
                        let fileReader = new FileReader();
                        fileReader.onload = function(e) {
                            previewContainer.append(`<img src="${e.target.result}" class="img-thumbnail">`);
                        };
                        fileReader.readAsDataURL(files[i]);
                    }
                }
            });
        });
    </script>
</body>
</html>
