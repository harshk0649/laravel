<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .table-responsive {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }
        .btn-animated {
            transition: all 0.3s ease-in-out;
        }
        .btn-animated:hover {
            transform: scale(1.05);
        }
        .modal-content {
            border-radius: 1rem;
        }
        .dropdown-toggle::after {
            margin-left: 0.5rem;
        }
    </style>
</head>
<body class="container py-4">

@if(session('success'))
    <div class="alert alert-success text-center" id="successMessage">{{ session('success') }}</div>
    <script>
        setTimeout(() => document.getElementById('successMessage').style.display = 'none', 3000);
    </script>
@endif

<!-- Manage Dropdown -->
<div class="d-flex justify-content-center mb-4">
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle px-4 py-2 rounded-pill shadow-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog me-1"></i> Select to Manage
        </button>
        <ul class="dropdown-menu text-center shadow" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item fw-semibold py-2" href="{{ Auth::user()->role === 'super_admin' ? route('super_admin.dashboard') : route('user.dashboard') }}"><i class="fas fa-users me-2"></i>Users & Admin</a></li>
            <li><a class="dropdown-item fw-semibold py-2" href="{{ route('brands.index') }}"><i class="fas fa-tags me-2"></i>Brands</a></li>
            <li><a class="dropdown-item fw-semibold py-2" href="{{ route('items.category') }}"><i class="fas fa-layer-group me-2"></i>Categories</a></li>
        </ul>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="javascript:history.back()" class="btn btn-secondary btn-animated">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <h2 class="fw-bold mb-0 text-center flex-grow-1">Categories List</h2>

    <a href="{{ Auth::user()->role === 'super_admin' ? route('super_admin.dashboard') : route('user.dashboard') }}"
       class="btn btn-danger btn-animated">
        <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
    </a>
</div>



<!-- Search Bar -->
<form action="{{ route('items.category') }}" method="get" class="mb-4">
    <div class="input-group w-50 mx-auto shadow-sm">
        <input type="text" name="search" class="form-control" placeholder="Search Category" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
    </div>
</form>

<!-- Add Category Button -->
<div class="text-center mb-4">
    <button class="btn btn-success px-4 btn-animated shadow" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fa fa-plus me-2"></i>Add New Category
    </button>
</div>

<!-- Categories Table -->
@if($categories->isNotEmpty())
    <div class="table-responsive p-3">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1 btn-animated" data-id="{{ $category->category_id }}" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-animated" onclick="return confirm('Are you sure?');">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted text-center mt-4">No categories found.</p>
@endif

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addCategoryModalLabel"><i class="fa fa-plus me-2"></i>Add New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="category_name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editCategoryForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editCategoryModalLabel"><i class="fa fa-edit me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="edit_category_name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Category Edit Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('button[data-bs-target="#editCategoryModal"]').forEach(button => {
            button.addEventListener('click', function () {
                const categoryId = this.getAttribute('data-id');
                document.getElementById('editCategoryForm').action = `/categories/${categoryId}`;
                fetch(`/categories/${categoryId}/edit`)
                    .then(res => res.json())
                    .then(data => document.getElementById('edit_category_name').value = data.category_name)
                    .catch(err => console.error('Fetch error:', err));
            });
        });
    });
</script>

</body>
</html>
