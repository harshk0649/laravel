<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand List</title>

    <link rel="stylesheet" href="{{ asset('css/brand-styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="d-flex justify-content-center mt-2">
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

<div class="brand-list-container animate__animated animate__fadeIn">
    <h2 class="animate__animated animate__fadeIn">Brand List</h2>

    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif

    <a href="{{ Auth::user()->role === 'super_admin' ? route('super_admin.dashboard') : route('user.dashboard') }}"
       class="btn btn-danger btn-animated mb-3">
        Back to Dashboard
    </a>

    <div class="add-brand d-flex justify-content-center">
        <a href="{{ route('brands.new') }}" class="btn btn-success mb-3 btn-animated">Add Brand</a>
    </div>

    <table class="table table-responsive table-bordered">
        <thead>
            <tr>
                <th>Brand ID</th>
                <th>Brand Name</th>
                <th>Logo</th>
                <th>Contact Email</th>
                <th>Website</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($brands as $brand)
                <tr>
                    <td>{{ $brand->brand_id }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>
                        @if ($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="Brand Logo" width="80" height="50">
                        @else
                            <span>No Logo</span>
                        @endif
                    </td>
                    <td>{{ $brand->contact_mail }}</td>
                    <td class="website-column">
                        @if ($brand->brand_web)
                            <a href="{{ $brand->brand_web }}" target="_blank">{{ $brand->brand_web }}</a>
                        @else
                            <span>N/A</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editBrandModal{{ $brand->brand_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteBrandModal{{ $brand->brand_id }}">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editBrandModal{{ $brand->brand_id }}" tabindex="-1" aria-labelledby="editBrandLabel{{ $brand->brand_id }}" aria-hidden="true">
                    <div class="modal-dialog bg-white">
                        <form action="{{ route('brands.update', $brand->brand_id) }}" method="POST" enctype="multipart/form-data" class="modal-content bg-white">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Brand</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Brand Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="contact_mail" class="form-control" value="{{ $brand->contact_mail }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="brand_web" class="form-control" value="{{ $brand->brand_web }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    @if ($brand->logo)
                                        <div class="mt-2">
                                            <strong>Current Logo:</strong><br>
                                            <img src="{{ asset('storage/' . $brand->logo) }}" class="img-thumbnail" width="100">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteBrandModal{{ $brand->brand_id }}" tabindex="-1" aria-labelledby="deleteBrandLabel{{ $brand->brand_id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-danger bg-opacity-10 border-bottom-0">
                                <h5 class="modal-title text-danger fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Deletion
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <div class="mb-4">
                                    <i class="bi bi-trash-fill text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-3">Are you sure you want to delete the brand <span class="text-danger">{{ $brand->name }}</span>?</h6>
                                <p class="text-muted small">This action cannot be undone and may affect related products or data.</p>
                            </div>
                            <div class="modal-footer border-top-0 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                </button>
                                <form action="{{ route('brands.delete', $brand->brand_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash me-1"></i>Delete Brand
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No brands available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
