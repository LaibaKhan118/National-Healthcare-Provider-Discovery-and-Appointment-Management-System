{{-- resources/views/doctor/appointments/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
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
                <a href="{{ route('doctor.appointments.index') }}" class="btn btn-sm btn-secondary mb-3">← Back</a>

                <h2 class="mb-4">Appointment Details</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Patient Information</h5>
                                <p><strong>Name:</strong> {{ $appointment->first_name }} {{ $appointment->last_name }}</p>
                                <p><strong>Email:</strong> {{ $appointment->email }}</p>
                                <p><strong>Phone:</strong> {{ $appointment->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Appointment Information</h5>
                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</p>
                                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                                <p><strong>Status:</strong> <span class="badge bg-{{ $appointment->appointment_status === 'pending' ? 'warning' : ($appointment->appointment_status === 'completed' ? 'success' : 'secondary') }}">{{ ucfirst($appointment->appointment_status) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($appointment->appointment_status === 'pending')
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Mark Appointment Status</h5>
                            <form action="{{ route('doctor.appointments.mark', $appointment->appointment_id) }}" method="POST" class="row g-3">
                                @csrf
                                <div class="col-md-6">
                                    <select class="form-select" name="status" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="completed">Mark as Completed</option>
                                        <option value="cancelled">Mark as Cancelled</option>
                                        <option value="no_show">Mark as No Show</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($appointment->appointment_status === 'completed')
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Medical Notes</h5>

                            @if ($note)
                                <form action="{{ route('doctor.appointments.addNote', $appointment->appointment_id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Update Notes/Prescription</label>
                                        <textarea class="form-control" name="note_content" rows="6" required>{{ $note->note_content }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Note</button>
                                </form>
                            @else
                                <form action="{{ route('doctor.appointments.addNote', $appointment->appointment_id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Add Notes/Prescription for Patient</label>
                                        <textarea class="form-control" name="note_content" rows="6" required placeholder="Add medical notes, prescriptions, or follow-up instructions..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Note</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
