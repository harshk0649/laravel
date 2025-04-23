<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Brand</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/brand-form.css') }}">
</head>
<body>

<div class="d-flex justify-content-center mt-3">
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle px-3 py-2 rounded-pill shadow" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog"></i> Select to Manage
        </button>
        <ul class="dropdown-menu text-center animate__animated animate__fadeIn" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item fw-bold py-2" href="{{ Auth::user()->role === 'super_admin' ? route('super_admin.dashboard') : route('user.dashboard') }}"><i class="fas fa-users"></i> Users & Admin</a></li>
            <li><a class="dropdown-item fw-bold py-2" href="{{ route('brands.index') }}"><i class="fas fa-tags"></i> Brands</a></li>
            <li><a class="dropdown-item fw-bold py-2" href="{{ route('items.category') }}"><i class="fas fa-tags"></i> Categories</a></li>
        </ul>
    </div>
</div>

<div class="container mt-4">
    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>

        <a href="{{ Auth::user()->role === 'super_admin' ? route('super_admin.dashboard') : route('user.dashboard') }}"
           class="btn btn-danger">
            <i class="fas fa-tachometer-alt me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="brand-form-container">
        <div class="header mb-3 d-flex align-items-center justify-content-between">
            <h2><i class="fa-solid fa-plus me-2"></i>Add New Brand</h2>
            <i class="fa-solid fa-moon dark-mode-toggle" onclick="toggleDarkMode()" role="button"></i>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-3">
                <label for="name"><i class="fa-solid fa-tag me-1"></i> Brand Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter brand name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="logo"><i class="fa-solid fa-image me-1"></i> Brand Logo</label>
                <input type="file" name="logo" id="logo" class="form-control">
            </div>

            <div class="form-group mb-3">
                <label for="contact_mail"><i class="fa-solid fa-envelope me-1"></i> Contact Email</label>
                <input type="email" name="contact_mail" id="contact_mail" class="form-control" placeholder="Enter email" value="{{ old('contact_mail') }}" required>
            </div>

            <div class="form-group mb-4">
                <label for="brand_web"><i class="fa-solid fa-globe me-1"></i> Brand Website</label>
                <input type="url" name="brand_web" id="brand_web" class="form-control" placeholder="Enter website URL" value="{{ old('brand_web') }}">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success px-4 py-2">
                    <i class="fa-solid fa-plus me-2"></i>Add Brand
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>
</html>
