<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyShop</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/ecommerce.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">MyShop</a>
        <form class="d-flex mx-auto w-50" action="{{ route('search') }}" method="GET">            <input class="form-control me-2 rounded-pill px-4" name="query" type="search" placeholder="Search for products, brands, categories..." aria-label="Search">
            <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
        </form>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item me-3">
                <a class="nav-link" href="#"><i class="fas fa-box"></i> Orders</a>
            </li>
            <li class="nav-item me-3">
                <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">{{ session('cart_count', 0) }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Sign In</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Carousel -->
<div id="mainCarousel" class="carousel slide mt-3" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($carouselProducts as $index => $carousel)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/' . $carousel->image) }}" class="d-block w-100" style="height: 400px; object-fit: cover; border-radius: 12px;" alt="Carousel Image">
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Brand Logos -->
<div class="container mt-5 mb-4 text-center">
    <div class="row justify-content-center">
        @foreach($brands as $brand)
            <div class="col-3 col-md-2 mb-3">
                <img src="{{ asset('storage/' . $brand->logo) }}" class="rounded-circle mb-1" style="height: 60px; width: 60px; object-fit: cover;" alt="{{ $brand->name }}">
                <div class="small fw-semibold">{{ $brand->name }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Product Cards -->
<div class="container mt-4">
    <div class="row">
        @foreach($products as $product)
            @php
                $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                $firstImage = $images[0] ?? 'default.jpg';
            @endphp
            <div class="col-md-4 mb-4">
                <div class="card product-card h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->product_id }}">
                    <img src="{{ asset('storage/' . $firstImage) }}" class="card-img-top" style="height: 220px; object-fit: contain; background: #fff;" alt="{{ $product->name }}">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 80) }}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">₹{{ $product->sale_price ?? $product->price }}</span>
                        </div>
                        <!-- <div class="d-flex justify-content-between align-items-start">
    <p class="price text-decoration-line-through text-muted small mb-0">
        Price: ₹{{ number_format($product->price, 2) }}
    </p>
</div> -->

                        <div class="modal-footer flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center" >
                                
                            </div>
                            <form action="{{ route('cart.add', $product->product_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                <input type="hidden" name="quantity" value="1">
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-sm btn-primary">Add to Cart</button>
                                    <a href="#" class="btn btn-sm btn-success">Buy Now</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="productModal{{ $product->product_id }}" tabindex="-1" aria-labelledby="productModalLabel{{ $product->product_id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $product->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($images)
                                <div id="carousel{{ $product->product_id }}" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($images as $key => $image)
                                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Image {{ $key }}">
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
                            <p><strong>Brands:</strong>
                @foreach($product->brands as $brand)
                    <span>{{ $brand->name }}</span>@if(!$loop->last), @endif
                @endforeach
            </p>
                            <p><strong>Categories:</strong>
    @foreach($product->categories as $category)
        <span>{{ $category->category_name }}</span>@if(!$loop->last), @endif
    @endforeach
</p>

                            <p class="text-danger"><strong>Sale Price:</strong> ₹{{ number_format($product->sale_price, 2) }}</p>
                            <p class="price text-decoration-line-through text-muted">Price: ₹{{ number_format($product->price, 2) }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-{{ $product->status === 'available' ? 'success' : 'danger' }}">{{ ucfirst($product->status) }}</span></p>
                            <p><strong>Quantity:</strong> {{ $product->quantity }} left</p>
                            <p><strong>Description:</strong> {{ $product->description }}</p>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex align-items-center gap-2">
                                <label for="quantity{{ $product->product_id }}" class="form-label mb-0"><strong>Quantity:</strong></label>
                                <div class="input-group" style="width: 120px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="decrementQty('{{ $product->product_id }}')">-</button>
                                    <input type="number" name="quantity" id="quantity{{ $product->product_id }}" class="form-control text-center" value="1" min="1" max="{{ $product->quantity }}">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="incrementQty('{{ $product->product_id }}')">+</button>
                                </div>
                            </div>
                            <form action="{{ route('cart.add', $product->product_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" id="hidden-quantity{{ $product->product_id }}" value="1">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                            <a href="#" class="btn btn-success">Buy Now</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4 text-center">
    <p class="mb-0">© 2025 MyShop. All rights reserved. | Built with Laravel</p>
</footer>

<!-- JS -->
<script src="{{ asset('js/ecommerce.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
