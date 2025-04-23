{{-- resources/views/dashboard/superadmin/edit-user.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/supeadmin-blade.css') }}">
</head>
<body>
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <a href="{{ Auth::user()->role === 'super_admin' ? route('super_admin.dashboard') : route('user.dashboard') }}" class="btn btn-danger position-absolute top-0 end-0 m-3">Back to Dashboard</a>

    <!-- Add Product Button -->
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="fas fa-plus-circle me-1"></i> Add Product
    </button>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('superadmin.product.add', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row g-3" style="max-height: 70vh; overflow-y: auto; padding-right: 10px;">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Product Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Brand</label>
                            <select class="form-select" name="brand_id" required>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->brand_id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Category</label>
                            <select class="form-select" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sale Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" name="sale_price" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Product Images</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" class="form-control" maxlength="500" rows="4" required></textarea>
                            <p class="char-count text-muted">0 / 500 characters</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Product</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Form -->
    <form action="{{ route('superadmin.user.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">User Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">User Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">User Role</label>
            <select name="role" class="form-select" id="role" required onchange="updateDropdownColor()">
                <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>

    <!-- Products Section -->
    <h2 class="mt-5 mb-3">Products Listed by {{ $user->name }}</h2>
    <div class="row">
        @foreach($products as $product)
            @php
                $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
            @endphp
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if(!empty($images) && is_array($images))
                        <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $product->name }}">
                    @else
                        <img src="https://placehold.co/300x200?text=No+Image" class="card-img-top" alt="No Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">
                            @if($product->sale_price)
                                <strong>Sale Price:</strong> Rs. {{ $product->sale_price }}<br>
                            @endif
                            <strong>Price:</strong> Rs. {{ $product->price }}<br>
                            <strong>Status:</strong> {{ ucfirst($product->status) }}<br>
                            <strong>Quantity:</strong> {{ $product->quantity }}
                        </p>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <form action="{{ route('superadmin.product.delete', $product->product_id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->product_id }}">Edit</button>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal{{ $product->product_id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('superadmin.product.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sale Price</label>
                                    <input type="number" name="sale_price" value="{{ $product->sale_price }}" step="0.01" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Price</label>
                                    <input type="number" name="price" value="{{ $product->price }}" step="0.01" class="form-control" required>
                                </div>
                               
                                <div class="mb-2">
                                                                    <small class="text-muted">Brand:</small>
                                                                    <span class="fw-semibold">{{ $product->brands->pluck('name')->implode(', ') ?? 'N/A' }}</span>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <small class="text-muted">Category:</small>
                                                                    <span class="fw-semibold">{{ $product->categories->pluck('category_name')->implode(', ') ?? 'N/A' }}</span>
                                                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="available" {{ $product->status === 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="unavailable" {{ $product->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="3" class="form-control">{{ $product->description }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Update Images</label>
                                    <input type="file" name="images[]" class="form-control" multiple>
                                    @if(is_array($images) && count($images))
                                        <div class="mt-2">
                                            @foreach ($images as $image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="Product Image" class="img-thumbnail" width="100">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update Product</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    function updateDropdownColor() {
        let dropdown = document.getElementById('role');
        let colorMap = {
            'super_admin': { bg: '#FFCCCC', text: '#800000' },
            'admin': { bg: '#FFFACD', text: '#8B8000' },
            'user': { bg: '#CCFFCC', text: '#006400' }
        };
        const selected = dropdown.value;
        dropdown.style.backgroundColor = colorMap[selected].bg;
        dropdown.style.color = colorMap[selected].text;
    }

    document.addEventListener("DOMContentLoaded", () => {
        updateDropdownColor();
        const description = document.getElementById("description");
        const charCount = document.querySelector(".char-count");
        if (description && charCount) {
            description.addEventListener("input", () => {
                charCount.textContent = `${description.value.length} / 500 characters`;
            });
        }
    });
</script>
</body>
</html>
