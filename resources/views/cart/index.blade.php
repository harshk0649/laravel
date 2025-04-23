<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        body {
            background-color: #f8f9fa;
        }
        .cart-image {
            height: 60px;
            width: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
        }
        .container {
            margin-top: 90px;
        }
        .table td {
            vertical-align: middle;
        }
        .spinner-border-sm {
            display: none;
        }
        .auto-submit-form.submitting .spinner-border-sm {
            display: inline-block;
        }
        .quantity-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40,167,69,.25);
        }
        @media (max-width: 768px) {
            .cart-image {
                height: 50px;
                width: 50px;
            }
            .navbar-brand {
                font-size: 20px;
            }
            .table {
                font-size: 14px;
            }
            .btn-sm {
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/home') }}">MyShop</a>
        <form class="d-flex mx-auto w-50 d-none d-md-flex">
            <input class="form-control me-2 rounded-pill px-4" type="search" placeholder="Search products, brands..." aria-label="Search">
            <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
        </form>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item me-3">
                <a class="nav-link" href="#"><i class="fas fa-box"></i> Orders</a>
            </li>
            <li class="nav-item me-3">
                <a class="nav-link active position-relative" href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                        {{ $cartItems->count() }}
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Sign In</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Cart Section -->
<div class="container">
    <h2 class="mb-4 fw-bold">Your Cart</h2>

    @if($cartItems->count() > 0)
        @php $total = 0; @endphp
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th style="width: 120px;">Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                    @php 
                        $subtotal = $item->price * $item->quantity;
                        $total += $subtotal;
                        $images = json_decode($item->images, true);
                        $products = is_array($images) ? $images[0] : $item->images;
                    @endphp
                    <tr class="cart-row">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/products' . $images) }}" class="cart-image" alt="Product">
                                <div>{{ $item->name }}</div>
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex auto-submit-form">
                                @csrf
                                <input type="hidden" name="cart_id" value="{{ $item->cart_id }}">
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm text-center quantity-input me-1">
                                <div class="spinner-border spinner-border-sm text-success"></div>
                            </form>
                        </td>
                        <td>₹{{ number_format($item->price, 2) }}</td>
                        <td>₹{{ number_format($subtotal, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cart_id" value="{{ $item->cart_id }}">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end mt-3">
            <h5 class="fw-bold">Total: ₹{{ number_format($total, 2) }}</h5>
            <a href="#" class="btn btn-success mt-2">Proceed to Checkout</a>
        </div>
    @else
        <div class="alert alert-info text-center">
            Your cart is empty. <a href="{{ url('/home') }}">Continue Shopping</a>
        </div>
    @endif
</div>

<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4 text-center">
    <p class="mb-0">© 2025 MyShop. All rights reserved. | Built with Laravel</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function () {
            const form = this.closest('form');
            form.classList.add('submitting');

            setTimeout(() => {
                form.submit();
            }, 200); // Slight delay to show spinner
        });
    });
</script>

</body>
</html>
