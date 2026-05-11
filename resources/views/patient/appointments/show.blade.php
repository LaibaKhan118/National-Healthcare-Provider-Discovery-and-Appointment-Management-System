{{-- resources/views/patient/appointments/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #1a3a5a !important; }
        .sidebar { background: #2d5a7a; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #3d6a8a; }
        .sidebar a.active { background: #0d3a5a; border-left: 4px solid #00aaff; }
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
                    <a href="{{ route('patient.dashboard') }}">Dashboard</a>
                    <a href="{{ route('patient.search') }}">Search Doctors</a>
                    <a href="{{ route('patient.profile.edit') }}">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-sm btn-secondary mb-3">← Back</a>

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
                                <h5 class="card-title">Doctor Information</h5>
                                <p><strong>Name:</strong> {{ $appointment->first_name }} {{ $appointment->last_name }}</p>
                                <p><strong>Specialization:</strong> {{ $appointment->specialization ?? '-' }}</p>
                                <p><strong>Email:</strong> {{ $appointment->email }}</p>
                                <p><strong>Consultation Fee:</strong> Rs. {{ $appointment->consultation_fee ?? '-' }}</p>
                                <p><strong>City:</strong> {{ $appointment->city ?? '-' }}</p>
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
                            <p class="text-muted">This appointment is confirmed. You will visit the doctor on the scheduled date and time.</p>
                            <form action="{{ route('patient.appointments.cancel', $appointment->appointment_id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this appointment?')">Cancel Appointment</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($appointment->appointment_status === 'completed')
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Medical Notes</h5>
                            @if ($note)
                                <div class="alert alert-info">
                                    <strong>Doctor's Notes:</strong>
                                    <p class="mt-2">{{ $note->note_content }}</p>
                                </div>
                            @else
                                <p class="text-muted">No notes from the doctor yet.</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($appointment->appointment_status === 'completed' && !$review)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Leave a Review</h5>
                            <p class="text-muted">Help other patients by sharing your experience with this doctor.</p>
                            <a href="{{ route('patient.reviews.create', $appointment->appointment_id) }}" class="btn btn-primary">Write Review</a>
                        </div>
                    </div>
                @elseif ($review)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Your Review</h5>
                            <p><strong>Rating:</strong> <span class="badge bg-info">{{ $review->rating }} ★</span></p>
                            @if ($review->comment)
                                <p><strong>Comment:</strong> {{ $review->comment }}</p>
                            @endif
                            <p class="text-muted small">Submitted on {{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
