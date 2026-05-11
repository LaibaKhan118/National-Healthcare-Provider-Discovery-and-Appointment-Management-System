{{-- resources/views/patient/reviews/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #1a3a5a !important; }
        .sidebar { background: #2d5a7a; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #3d6a8a; }
        .sidebar a.active { background: #0d3a5a; border-left: 4px solid #00aaff; }
        .star-rating { font-size: 2rem; cursor: pointer; }
        .star-rating .star { color: #ddd; display: inline-block; margin: 0 5px; }
        .star-rating .star.active { color: #ffc107; }
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
                <a href="{{ route('patient.appointments.show', $appointment->appointment_id) }}" class="btn btn-sm btn-secondary mb-3">← Back</a>

                <h2 class="mb-4">Leave a Review</h2>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</h5>
                                <p class="text-muted">{{ $doctor->specialization ?? '-' }}</p>
                                <hr>

                                <form action="{{ route('patient.reviews.store', $appointment->appointment_id) }}" method="POST">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="form-label"><strong>How would you rate your experience?</strong></label>
                                        <div class="star-rating mb-3">
                                            <input type="hidden" name="rating" id="rating" value="0" required>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="star" data-value="{{ $i }}" onclick="setRating({{ $i }})">★</span>
                                            @endfor
                                        </div>
                                        <small class="text-muted">Click to select stars (1-5)</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Your Comment (Optional)</label>
                                        <textarea class="form-control" name="comment" rows="5" maxlength="500" placeholder="Share your experience with this doctor..."></textarea>
                                        <small class="text-muted">Max 500 characters</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                    <a href="{{ route('patient.appointments.show', $appointment->appointment_id) }}" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setRating(value) {
            document.getElementById('rating').value = value;
            const stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < value) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
