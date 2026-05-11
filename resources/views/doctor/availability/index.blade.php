{{-- resources/views/doctor/availability/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability</title>
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
                    <a href="{{ route('doctor.appointments.index') }}">Appointments</a>
                    <a href="{{ route('doctor.availability.index') }}" class="active">Availability</a>
                    <a href="{{ route('doctor.profile.edit') }}">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Manage Your Availability</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Add New Time Slot</h5>
                        <form action="{{ route('doctor.availability.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Day of Week</label>
                                <select class="form-select" name="day_of_week" required>
                                    <option value="">-- Select Day --</option>
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                    <option value="7">Sunday</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Add Slot</button>
                            </div>
                        </form>
                    </div>
                </div>

                <h4 class="mb-3">Your Weekly Schedule</h4>

                @foreach ($daysArray as $dayNum => $day)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>{{ $day['name'] }}</strong>
                        </div>
                        <div class="card-body">
                            @if ($day['slots']->count() > 0)
                                <div class="row g-2">
                                    @foreach ($day['slots'] as $slot)
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center p-2 border rounded" style="background: {{ $slot->is_booked ? '#fff3cd' : '#d4edda' }}">
                                                <span>
                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                    @if ($slot->is_booked)
                                                        <span class="badge bg-warning ms-2">Booked</span>
                                                    @endif
                                                </span>
                                                @if (!$slot->is_booked)
                                                    <form action="{{ route('doctor.availability.destroy', $slot->availability_id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this slot?')">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No slots available</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
