<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-blade.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .table-responsive { overflow-x: auto; }
        .modal-body ul { padding: 0; }
        .modal-body ul li { list-style: none; }



        .hover-zoom {
        max-width: 100%;
        height: auto;
        transition: transform 0.3s ease-in-out;
        cursor: pointer;
    }
    .hover-zoom:hover {
        transform: scale(1.1);
    }


    .image-container {
        width: 120px;  /* Fixed width */
        height: 120px; /* Fixed height */
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    </style>
</head>
<body class="container-fluid">
@if(session('success'))
    <div class="alert alert-success" id="successMessage">{{ session('success') }}</div>
    <script>
        setTimeout(() => document.getElementById('successMessage').style.display = 'none', 3000);
    </script>
@endif

<!-- Centered Header -->
<div class="d-flex flex-column align-items-center justify-content-center text-center py-4">
    <h1 class="fw-bold">Welcome to Admin Dashboard</h1>
    <h2 class="text-danger">{{ $adminName }}</h2>
</div>

<div class="d-flex justify-content-between align-items-center">
    <a href="javascript:history.back()" class="btn mb-3">
        <i class="fa-solid fa-arrow-left" style="color: black; font-size: 20px;"></i>
    </a>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Log Out</button>
    </form>
</div>

<!-- Search Form -->
<form action="{{ route('admin.search') }}" method="get" class="text-center">
    <div class="input-group mb-3 w-50 mx-auto">
        <input type="text" name="search" class="form-control" placeholder="Search With Name" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<h2 class="mt-4">List of Users & Admin:</h2>
@if($users->isEmpty())
    <p class="text-muted">No users found.</p>
@endif

<!-- Table -->
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}">View</button>
                        <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modals -->
@foreach($users as $user)
<div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1" aria-labelledby="userModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">About {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div> 
            <div class="modal-body">
                <h5>User: {{ $user->name }}</h5>
                <h5>Role: {{ $user->role }}</h5>
                <h6>Uploaded Files:</h6>
                <div class="row">
                    @foreach ($user->files as $file)
                        <div class="col-4 mb-3 text-center">
                            @if (in_array(pathinfo($file->file_path, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg']))
                                <div class="image-container">
                                    <img src="{{ asset('storage/' . $file->file_path) }}" class="img-thumbnail hover-zoom">
                                </div>
                            @else
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-primary">View File</a>
                            @endif
                            <p class="small text-muted" style="cursor: pointer;">{{ $file->description }}</p>
                            </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

</body>
</html>
