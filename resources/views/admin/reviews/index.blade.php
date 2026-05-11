{{-- resources/views/admin/reviews/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background-color: #1a3a3a !important; }
        .sidebar { background: #2d5a5a; min-height: 100vh; position: sticky; top: 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 0.75rem 1.5rem; display: block; }
        .sidebar a:hover { background: #3d6a6a; }
        .sidebar a.active { background: #0d4f4f; border-left: 4px solid #00aaaa; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Healthcare Admin</span>
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
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.doctors.index') }}">Doctors</a>
                    <a href="{{ route('admin.reviews.index') }}" class="active">Reviews</a>
                    <a href="{{ route('admin.specializations.index') }}">Specializations</a>
                    <a href="{{ route('admin.hospitals.index') }}">Hospitals</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h2 class="mb-4">Manage Reviews</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <a href="{{ route('admin.reviews.index', ['rating' => 'all']) }}" class="btn btn-sm {{ $filterRating === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('admin.reviews.index', ['rating' => 5]) }}" class="btn btn-sm {{ $filterRating === '5' ? 'btn-primary' : 'btn-outline-primary' }}">5 Stars</a>
                    <a href="{{ route('admin.reviews.index', ['rating' => 4]) }}" class="btn btn-sm {{ $filterRating === '4' ? 'btn-primary' : 'btn-outline-primary' }}">4 Stars</a>
                    <a href="{{ route('admin.reviews.index', ['rating' => 3]) }}" class="btn btn-sm {{ $filterRating === '3' ? 'btn-primary' : 'btn-outline-primary' }}">3 Stars</a>
                    <a href="{{ route('admin.reviews.index', ['rating' => 2]) }}" class="btn btn-sm {{ $filterRating === '2' ? 'btn-primary' : 'btn-outline-primary' }}">2 Stars</a>
                    <a href="{{ route('admin.reviews.index', ['rating' => 1]) }}" class="btn btn-sm {{ $filterRating === '1' ? 'btn-primary' : 'btn-outline-primary' }}">1 Star</a>
                </div>

                <div class="card">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Patient</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reviews as $review)
                                <tr>
                                    <td>{{ $review->doctor_name }}</td>
                                    <td>{{ $review->patient_name }}</td>
                                    <td><span class="badge bg-info">{{ $review->rating }} ★</span></td>
                                    <td style="max-width: 300px; word-wrap: break-word;"><small>{{ $review->comment ?? 'No comment' }}</small></td>
                                    <td><small>{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y') }}</small></td>
                                    <td>
                                        <form action="{{ route('admin.reviews.destroy', $review->review_id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete review?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">No reviews found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $reviews->links() }}
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
