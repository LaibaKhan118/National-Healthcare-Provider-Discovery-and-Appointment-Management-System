{{-- resources/views/admin/specializations/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specializations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #1a3a3a !important; }
        .sidebar { background: #2d5a5a; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #3d6a6a; }
        .sidebar a.active { background: #0d4f4f; border-left: 4px solid #00aaaa; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Healthcare Admin</span>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar">
                <div class="sidebar-sticky pt-3">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.doctors.index') }}">Doctors</a>
                    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
                    <a href="{{ route('admin.specializations.index') }}" class="active">Specializations</a>
                    <a href="{{ route('admin.hospitals.index') }}">Hospitals</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Manage Specializations</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $spec ? 'Edit' : 'Add New' }} Specialization</h5>
                                <form action="{{ $spec ? route('admin.specializations.update', $spec->specialization_id) : route('admin.specializations.store') }}" method="POST">
                                    @csrf
                                    @if ($spec)
                                        @method('PUT')
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="specialization_name" value="{{ $spec->specialization_name ?? '' }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">{{ $spec ? 'Update' : 'Add' }}</button>
                                    @if ($spec)
                                        <a href="{{ route('admin.specializations.index') }}" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Specializations</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Doctors</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($specializations as $s)
                                                <tr>
                                                    <td>{{ $s->specialization_name }}</td>
                                                    <td><span class="badge bg-info">{{ $s->doctor_count }}</span></td>
                                                    <td>
                                                        <a href="{{ route('admin.specializations.index', ['edit' => $s->specialization_id]) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('admin.specializations.destroy', $s->specialization_id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" {{ $s->doctor_count > 0 ? 'disabled' : '' }}>Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-3 text-muted">No specializations</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
