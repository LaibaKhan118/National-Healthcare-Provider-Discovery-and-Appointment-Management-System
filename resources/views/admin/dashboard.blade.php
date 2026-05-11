{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #1a3a3a !important; }
        .sidebar { background: #2d5a5a; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #3d6a6a; }
        .sidebar a.active { background: #0d4f4f; border-left: 4px solid #00aaaa; }
        .card { border: 1px solid #dee2e6; }
        .stat-card { text-align: center; padding: 1.5rem; }
        .stat-card h3 { font-size: 2rem; margin-bottom: 0.5rem; }
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
                    <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
                    <a href="{{ route('admin.doctors.index') }}">Doctors</a>
                    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
                    <a href="{{ route('admin.specializations.index') }}">Specializations</a>
                    <a href="{{ route('admin.hospitals.index') }}">Hospitals</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Dashboard</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-primary">
                            <h3 class="text-primary">{{ $stats->total_users ?? 0 }}</h3>
                            <p class="text-muted">Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-info">
                            <h3 class="text-info">{{ $stats->doctor_count ?? 0 }}</h3>
                            <p class="text-muted">Doctors</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-success">
                            <h3 class="text-success">{{ $stats->patient_count ?? 0 }}</h3>
                            <p class="text-muted">Patients</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-danger">
                            <h3 class="text-danger">{{ $stats->suspended_count ?? 0 }}</h3>
                            <p class="text-muted">Suspended</p>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4 mb-3">Pending Doctor Approvals</h4>
                <div class="card">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingDoctors as $doc)
                                <tr>
                                    <td>{{ $doc->first_name }} {{ $doc->last_name }}</td>
                                    <td>{{ $doc->email }}</td>
                                    <td>
                                        <a href="{{ route('admin.doctors.show', $doc->doctor_id) }}" class="btn btn-sm btn-primary">Review</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No pending doctors</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h4 class="mt-4 mb-3">Top Doctors</h4>
                <div class="card">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topDoctors as $doc)
                                <tr>
                                    <td>{{ $doc->first_name }} {{ $doc->last_name }}</td>
                                    <td>
                                        @if ($doc->avg_rating)
                                            <span class="badge bg-info">{{ number_format($doc->avg_rating, 1) }} ★</span>
                                        @else
                                            <span class="text-muted">No rating</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">No doctors found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h4 class="mt-4 mb-3">Recent Appointments</h4>
                <div class="card">
                    <table class="table table-striped mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentAppointments as $apt)
                                <tr>
                                    <td>{{ $apt->doctor_name }}</td>
                                    <td>{{ $apt->patient_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $apt->appointment_status === 'pending' ? 'warning' : ($apt->appointment_status === 'completed' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($apt->appointment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No appointments</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
