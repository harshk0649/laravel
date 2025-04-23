<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <script src="public/js/user.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-center flex-grow-1">Welcome, {{ Auth::user()->name }}!</h1>
        <i class="fa-solid fa-moon dark-mode-toggle" onclick="toggleDarkMode()"></i>
    </div>

    <form action="{{ route('logout') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>

    <p class="email-text text-center mt-3">
        <i class="fa-solid fa-envelope"></i> <strong>{{ Auth::user()->email }}</strong>
    </p>

    <div class="text-center mb-4">
        <input type="text" id="searchInput" placeholder="Search products..." class="form-control search-bar">
        <button class="btn btn-secondary mt-2" onclick="filterFiles()">Search</button>
    </div>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3 d-block text-center">Add New Product</a>

    <h2 class="text-center mb-4">Your Products</h2>

    @if(isset($products) && $products->count() > 0)
    <div class="row">
        @foreach ($products as $product)
        @if ($product->user_id === Auth::id())  
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card product-card h-100">
                    @php
                        $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                        $firstImage = $images && count($images) > 0 ? asset('storage/' . $images[0]) : 'https://via.placeholder.com/200';
                    @endphp
                    <img src="{{ $firstImage }}" alt="Product Image" class="product-img">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>

                        @if ($product->sale_price)
                            <p class="text-danger"><strong>Sale Price:</strong> Rs.{{ number_format($product->sale_price, 2) }}</p>
                        @endif
                        <p class="price text-decoration-line-through text-muted">
                            price: ₹{{ number_format($product->price, 2) }}
                        </p>
                        <p><strong>Status:</strong> {{ ucfirst($product->status) }}</p>
                        <p><strong>Quantity:</strong> {{ $product->quantity }}+ </p>
                        <p>{{ Str::limit($product->description, 80) }}</p>

                        <div class="btn-group mt-auto">
                            <button class="btn btn-info" onclick="showProductModal('{{ $product->product_id }}')">View</button>
                            <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Modal -->
            <div class="modal fade" id="productModal{{ $product->product_id }}" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Product: {{ $product->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($images)
                            <div id="carousel{{ $product->product_id }}" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($images as $key => $image)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 400px; object-fit: cover;">
                                    </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $product->product_id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $product->product_id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                            @endif

                            <p><strong>Brand:</strong> {{ $product->brands->pluck('name')->implode(', ') ?? 'N/A' }}</p>
                            <p><strong>Category:</strong> {{ $product->categories->pluck('category_name')->implode(', ') ?? 'N/A' }}</p>
                            <p class="text-danger"><strong>Sale Price:</strong> Rs.{{ number_format($product->sale_price, 2) }}</p>
                            <p class="price text-decoration-line-through text-muted">
                                price: ₹{{ number_format($product->price, 2) }}
                            </p> 
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $product->status == 'available' ? 'success' : 'danger' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </p>
                            <p><strong>Quantity:</strong> {{ $product->quantity }} + left</p>
                            <p><strong>Description:</strong> {{ $product->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @endforeach
    </div>
    @else
        <p class="text-center">No products found.</p>
    @endif
</div>

<script>
    function showProductModal(productId) {
        var modal = new bootstrap.Modal(document.getElementById('productModal' + productId));
        modal.show();
    }

    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
    }
</script>


</body>
</html>
