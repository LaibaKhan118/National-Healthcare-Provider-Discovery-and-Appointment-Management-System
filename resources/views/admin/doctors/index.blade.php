{{-- resources/views/admin/doctors/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
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
                    <a href="{{ route('admin.doctors.index') }}" class="active">Doctors</a>
                    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
                    <a href="{{ route('admin.specializations.index') }}">Specializations</a>
                    <a href="{{ route('admin.hospitals.index') }}">Hospitals</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Manage Doctors</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <a href="{{ route('admin.doctors.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        All Doctors
                    </a>
                    <a href="{{ route('admin.doctors.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Pending
                    </a>
                    <a href="{{ route('admin.doctors.index', ['status' => 'verified']) }}" class="btn btn-sm {{ $status === 'verified' ? 'btn-success' : 'btn-outline-success' }}">
                        Verified
                    </a>
                    <a href="{{ route('admin.doctors.index', ['status' => 'suspended']) }}" class="btn btn-sm {{ $status === 'suspended' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Suspended
                    </a>
                </div>

                <div class="card">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Experience</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($doctors as $doctor)
                                <tr>
                                    <td>{{ $doctor->first_name }} {{ $doctor->last_name }}</td>
                                    <td><small>{{ $doctor->email }}</small></td>
                                    <td>
                                        @if ($doctor->specializations)
                                            {{ $doctor->specializations }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $doctor->experience_years ?? '-' }} yrs</small></td>
                                    <td><small>Rs. {{ $doctor->consultation_fee ?? '-' }}</small></td>
                                    <td>
                                        @if (!$doctor->is_verified)
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif ($doctor->account_status === 'suspended')
                                            <span class="badge bg-danger">Suspended</span>
                                        @else
                                            <span class="badge bg-success">Verified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.doctors.show', $doctor->doctor_id) }}" class="btn btn-sm btn-primary">View</a>
                                        @if (!$doctor->is_verified)
                                            <form action="{{ route('admin.doctors.approve', $doctor->doctor_id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No doctors found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $doctors->links() }}
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
