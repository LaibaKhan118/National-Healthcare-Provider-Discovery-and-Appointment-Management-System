{{-- resources/views/patient/search.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Doctors</title>
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
                <h2 class="mb-4">Search Doctors</h2>

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('patient.search') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Specialization</label>
                                <select class="form-select" name="specialization">
                                    <option value="">All</option>
                                    @foreach ($specializations as $spec)
                                        <option value="{{ $spec }}" {{ $specialization === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <select class="form-select" name="city">
                                    <option value="">All</option>
                                    @foreach ($cities as $c)
                                        <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Min Rating</label>
                                <select class="form-select" name="min_rating">
                                    <option value="0" {{ $minRating == 0 ? 'selected' : '' }}>Any</option>
                                    <option value="1" {{ $minRating == 1 ? 'selected' : '' }}>1★ +</option>
                                    <option value="2" {{ $minRating == 2 ? 'selected' : '' }}>2★ +</option>
                                    <option value="3" {{ $minRating == 3 ? 'selected' : '' }}>3★ +</option>
                                    <option value="4" {{ $minRating == 4 ? 'selected' : '' }}>4★ +</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3">
                    @forelse ($doctors as $doctor)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {{ $doctor->first_name }} {{ $doctor->last_name }}
                                    </h5>
                                    <p class="card-text text-muted small">
                                        {{ $doctor->specialization ?? '-' }}
                                    </p>

                                    <div class="mb-3">
                                        <div>
                                            <span class="text-warning">★</span>
                                            <span class="small">
                                                @if ($doctor->review_count > 0)
                                                    {{ number_format($doctor->avg_rating, 1) }} ({{ $doctor->review_count }})
                                                @else
                                                    No reviews
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
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                No doctors found matching your criteria.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $doctors->links() }}
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
