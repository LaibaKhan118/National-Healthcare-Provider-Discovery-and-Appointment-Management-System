{{-- resources/views/patient/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #1a3a5a !important; }
        .sidebar { background: #2d5a7a; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #3d6a8a; }
        .sidebar a.active { background: #0d3a5a; border-left: 4px solid #00aaff; }
        .stat-card { text-align: center; padding: 1.5rem; }
        .stat-card h3 { font-size: 2rem; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Patient Portal</span>
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
                    <a href="{{ route('patient.dashboard') }}" class="active">Dashboard</a>
                    <a href="{{ route('patient.search') }}">Search Doctors</a>
                    <a href="{{ route('patient.profile.edit') }}">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Welcome, {{ $patient->user->first_name }}</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-primary">
                            <h3 class="text-primary">{{ $stats->total ?? 0 }}</h3>
                            <p class="text-muted">Total Appointments</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-warning">
                            <h3 class="text-warning">{{ $stats->pending_count ?? 0 }}</h3>
                            <p class="text-muted">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-success">
                            <h3 class="text-success">{{ $stats->completed_count ?? 0 }}</h3>
                            <p class="text-muted">Completed</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-info">
                            <h3 class="text-info">{{ $pendingReviews->count() }}</h3>
                            <p class="text-muted">Pending Reviews</p>
                        </div>
                    </div>
                </div>

                @if ($nextAppointment)
                    <h4 class="mt-4 mb-3">Your Next Appointment</h4>
                    <div class="card border-info">
                        <div class="card-body">
                            <p><strong>Doctor:</strong> {{ $nextAppointment->doctor_name }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('l, F d, Y') }}</p>
                            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($nextAppointment->appointment_time)->format('h:i A') }}</p>
                            <p><strong>Specialization:</strong> {{ $nextAppointment->specialization ?? '-' }}</p>
                            <a href="{{ route('patient.appointments.show', $nextAppointment->appointment_id) }}" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                @endif

                <h4 class="mt-4 mb-3">Appointment History</h4>
                <div class="card">
                    <table class="table table-striped mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Fee</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appointments as $apt)
                                <tr>
                                    <td>{{ $apt->doctor_name }}</td>
                                    <td><small>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $apt->appointment_status === 'pending' ? 'warning' : ($apt->appointment_status === 'completed' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($apt->appointment_status) }}
                                        </span>
                                    </td>
                                    <td><small>Rs. {{ $apt->consultation_fee ?? '-' }}</small></td>
                                    <td>
                                        <a href="{{ route('patient.appointments.show', $apt->appointment_id) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No appointments yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="{{ route('patient.search') }}" class="btn btn-primary">Search for Doctors</a>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
