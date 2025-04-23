<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/sadmin.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container-fluid">

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success text-center" id="successMessage">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('successMessage').style.display = 'none';
        }, 3000);
    </script>
@endif

<!-- Dropdown -->
<div class="d-flex justify-content-center mt-2">
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle px-3 py-2 rounded-pill shadow" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-cog"></i> Select to Manage
        </button>
        <ul class="dropdown-menu text-center">
            <li><a class="dropdown-item fw-bold py-2" href="#"><i class="fas fa-users"></i> Users & Admin</a></li>
            <li><a class="dropdown-item fw-bold py-2" href="{{ route('brands.index') }}"><i class="fas fa-tags"></i> Brands</a></li>
            <li><a class="dropdown-item fw-bold py-2" href="{{ route('items.category') }}"><i class="fas fa-tags"></i> Categories</a></li>
        </ul>
    </div>
</div>

<!-- Super Admin Header -->
<div class="text-center py-4">
    <h1 class="fw-bold">Welcome to Super Admin Dashboard</h1>
    <h2 class="text-danger">{{ $adminName }}</h2>
</div>

<!-- Back & Logout -->
<div class="d-flex justify-content-between align-items-center">
    <a href="javascript:history.back()" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Log Out</button>
    </form>
</div>

<!-- Search -->
<form action="{{ route('superadmin.search') }}" method="get" class="text-center my-4">
    <div class="input-group w-50 mx-auto">
        <input type="text" name="search" class="form-control" placeholder="Search With Name" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<!-- Add Buttons -->
<div class="d-flex justify-content-center gap-3 mb-4">
    <a href="{{ route('brands.new') }}" class="btn btn-success">Add Brand</a>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
</div>

<!-- Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label for="categoryName" class="form-label">Category Name</label>
                <input type="text" class="form-control" name="category_name" id="categoryName" required placeholder="Enter category name">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Users & Admins Table -->
<div class="container">
    <h2 class="mt-4">List of Users & Admins:</h2>

    @if($users->isEmpty())
        <p class="text-muted">No users found.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                @if($user->products && $user->products->count() > 0)
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#userProductsModal{{ $user->id }}">
                                        <i class="fa fa-eye"></i> View Products
                                    </button>
                                @else
                                    <span class="text-muted">No products</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('superadmin.user.edit', $user->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('superadmin.user.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Products Modal -->
                        <div class="modal fade" id="userProductsModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header bg-dark text-white rounded-top">
                                        <h5 class="modal-title">Products of {{ $user->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body bg-light">
                                        @if($user->products->count() > 0)
                                            <div class="row g-4">
                                                @foreach($user->products as $product)
                                                @php
                                            $images = is_array($product->images) ? $product->images : json_decode($product->images, true) ?? [];
                                                @endphp
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="card h-100 border-0 shadow-sm rounded-3">
                                                            @if(count($images) > 0)
                                                                <div class="position-relative">
                                                                    <img src="{{ asset('storage/' . $images[0]) }}"
                                                                         class="card-img-top rounded-top"
                                                                         style="height: 220px; object-fit: cover;">
                                                                    <div class="position-absolute top-0 end-0 m-2 badge bg-{{ $product->status == 'available' ? 'success' : 'danger' }}">
                                                                        {{ ucfirst($product->status) }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="card-body d-flex flex-column">
                                                                <h5 class="card-title text-dark">{{ $product->name }}</h5>

                                                                <div class="mb-2">
                                                                    <small class="text-muted">Brand:</small>
                                                                    <span class="fw-semibold">{{ $product->brands->pluck('name')->implode(', ') ?? 'N/A' }}</span>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <small class="text-muted">Category:</small>
                                                                    <span class="fw-semibold">{{ $product->categories->pluck('category_name')->implode(', ') ?? 'N/A' }}</span>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <small class="text-muted">Sale Price:</small>
                                                                    <span class="text-primary fw-bold fs-5">₹{{ number_format($product->sale_price, 2) }}</span>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <small class="text-muted">Quantity:</small>
                                                                    <span class="fw-semibold">{{ $product->quantity }}</span>
                                                                </div>

                                                                <div class="mt-auto">
                                                                    <small class="text-muted">Description:</small>
                                                                    <p class="small text-secondary mb-0">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                                                                </div>
                                                            </div>

                                                            @if(count($images) > 1)
                                                                <div class="card-footer bg-white">
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach(array_slice($images, 1) as $img)
                                                                            <img src="{{ asset('storage/' . $img) }}"
                                                                                 width="50" height="50"
                                                                                 class="rounded border"
                                                                                 style="object-fit: cover;">
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted text-center">No products available for this user.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</body>
</html>
