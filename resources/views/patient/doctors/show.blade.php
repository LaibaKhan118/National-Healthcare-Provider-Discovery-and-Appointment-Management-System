{{-- resources/views/patient/doctors/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
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
                    <a href="{{ route('patient.search') }}" class="active">Search Doctors</a>
                    <a href="{{ route('patient.profile.edit') }}">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <a href="{{ route('patient.search') }}" class="btn btn-sm btn-secondary mb-3">← Back</a>

                <h2 class="mb-4">{{ $doctor->first_name }} {{ $doctor->last_name }}</h2>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Profile Information</h5>
                                <p><strong>Specialization:</strong> {{ $doctor->specialization ?? '-' }}</p>
                                <p><strong>Experience:</strong> {{ $doctor->experience_years ?? '-' }} years</p>
                                <p><strong>City:</strong> {{ $doctor->city ?? '-' }}</p>
                                <p><strong>Consultation Fee:</strong> Rs. {{ $doctor->consultation_fee ?? '-' }}</p>
                                <p><strong>Hospital:</strong> {{ $doctor->hospital_affiliation ?? '-' }}</p>
                                @if ($doctor->bio)
                                    <p><strong>Bio:</strong> {{ $doctor->bio }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Rating & Reviews</h5>
                                <div class="text-center mb-3">
                                    <h3>{{ number_format($averageRating, 1) }}</h3>
                                    <p class="text-muted">out of 5</p>
                                    <p class="text-muted"><strong>{{ $reviewCount }}</strong> reviews</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Available Time Slots</h4>
                <div class="card mb-4">
                    <div class="card-body">
                        @if ($availableSlots->count() > 0)
                            <form action="{{ route('patient.appointments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="doctor_id" value="{{ $doctor->doctor_id }}">
                                <div class="mb-3">
                                    <label class="form-label">Select a time slot:</label>
                                    <select class="form-select" name="availability_id" required>
                                        <option value="">-- Select Time Slot --</option>
                                        @foreach ($availableSlots as $slot)
                                            <option value="{{ $slot->availability_id }}">
                                                {{ $dayNames[$slot->day_of_week] }}: {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </form>
                        @else
                            <p class="text-muted">No available time slots at the moment. Please check back later.</p>
                        @endif
                    </div>
                </div>

                @if ($reviews->count() > 0)
                    <h4 class="mb-3">Recent Reviews</h4>
                    <div class="card">
                        @foreach ($reviews as $r)
                            <div class="card-body border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $r->patient_name }}</strong>
                                        <span class="badge bg-info ms-2">{{ $r->rating }} ★</span>
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($r->created_at)->format('M d, Y') }} </small>
                                </div>
                                @if ($r->comment)
                                    <p class="mt-2 mb-0">{{ $r->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
