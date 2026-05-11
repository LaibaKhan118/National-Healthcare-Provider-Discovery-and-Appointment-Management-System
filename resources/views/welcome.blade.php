{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Healthcare System</a>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-light" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-light">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-md-8">
                <h1>Welcome to Healthcare System</h1>
                <p class="lead text-muted">Find and book appointments with verified healthcare providers</p>
                <a href="{{ route('patient.search') }}" class="btn btn-primary">Search Doctors</a>
            </div>
        </div>

        <h2 class="mb-4">Top Rated Doctors</h2>

        <div class="row g-3">
            @foreach ($topDoctors as $doctor)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $doctor->first_name }} {{ $doctor->last_name }}
                            </h5>
                            <p class="card-text text-muted small">
                                {{ $doctor->specialization }}
                            </p>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-warning">★</span>
                                    <span class="small">
                                        @if ($doctor->review_count > 0)
                                            {{ number_format($doctor->avg_rating, 1) }} ({{ $doctor->review_count }} reviews)
                                        @else
                                            No reviews yet
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if ($doctor->city)
                                <p class="card-text small"><strong>City:</strong> {{ $doctor->city }}</p>
                            @endif

                            @if ($doctor->consultation_fee)
                                <p class="card-text small"><strong>Fee:</strong> Rs. {{ number_format($doctor->consultation_fee) }}</p>
                            @endif

                            <a href="{{ route('patient.doctors.show', $doctor->doctor_id) }}" class="btn btn-sm btn-primary">View Profile</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-5 pt-5 border-top">
            <div class="col-md-12">
                <p class="text-muted text-center small">
                    Built with Laravel + MySQL | DB Systems Project
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
