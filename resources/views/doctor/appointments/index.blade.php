{{-- resources/views/doctor/appointments/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #2d5a3d !important; }
        .sidebar { background: #3d7a4d; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #4d8a5d; }
        .sidebar a.active { background: #1d4a2d; border-left: 4px solid #00ff88; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Doctor Portal</span>
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
                    <a href="{{ route('doctor.dashboard') }}">Dashboard</a>
                    <a href="{{ route('doctor.appointments.index') }}" class="active">Appointments</a>
                    <a href="{{ route('doctor.availability.index') }}">Availability</a>
                    <a href="{{ route('doctor.profile.edit') }}">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Manage Appointments</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <a href="{{ route('doctor.appointments.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Pending ({{ $statusCounts->pending_count ?? 0 }})
                    </a>
                    <a href="{{ route('doctor.appointments.index', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Completed ({{ $statusCounts->completed_count ?? 0 }})
                    </a>
                    <a href="{{ route('doctor.appointments.index', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status === 'cancelled' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Cancelled ({{ $statusCounts->cancelled_count ?? 0 }})
                    </a>
                    <a href="{{ route('doctor.appointments.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        All
                    </a>
                </div>

                <div class="card">
                    <table class="table table-striped mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appointments as $apt)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</td>
                                    <td>{{ $apt->patient_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $apt->appointment_status === 'pending' ? 'warning' : ($apt->appointment_status === 'completed' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($apt->appointment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('doctor.appointments.show', $apt->appointment_id) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No appointments</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $appointments->links() }}
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
