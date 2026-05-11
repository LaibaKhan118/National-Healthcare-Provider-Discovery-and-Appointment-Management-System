{{-- resources/views/doctor/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #2d5a3d !important; }
        .sidebar { background: #3d7a4d; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #4d8a5d; }
        .sidebar a.active { background: #1d4a2d; border-left: 4px solid #00ff88; }
        .stat-card { text-align: center; padding: 1.5rem; }
        .stat-card h3 { font-size: 2rem; margin-bottom: 0.5rem; }
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
                    <a href="{{ route('doctor.dashboard') }}" class="active">Dashboard</a>
                    <a href="{{ route('doctor.appointments.index') }}">Appointments</a>
                    <a href="{{ route('doctor.availability.index') }}">Availability</a>
                    <a href="{{ route('doctor.profile.edit') }}">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Welcome, Dr. {{ $doctor->user->last_name }}</h2>

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
                            <h3 class="text-info">{{ number_format($averageRating, 1) }}</h3>
                            <p class="text-muted">Avg Rating ({{ $reviewCount }})</p>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4 mb-3">Today's Appointments</h4>
                <div class="card">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($todayAppointments as $apt)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</td>
                                    <td>{{ $apt->patient_name }}</td>
                                    <td><small>{{ $apt->email }}</small></td>
                                    <td>
                                        <a href="{{ route('doctor.appointments.show', $apt->appointment_id) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No appointments today</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h4 class="mt-4 mb-3">Upcoming Appointments</h4>
                <div class="card">
                    <table class="table table-striped mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upcomingAppointments as $apt)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</td>
                                    <td>{{ $apt->patient_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No upcoming appointments</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="text-muted mt-4 text-center small">
                    Open Slots: {{ $openSlots }} | <a href="{{ route('doctor.availability.index') }}">Manage Availability</a>
                </p>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
