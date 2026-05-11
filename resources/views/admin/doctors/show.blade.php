{{-- resources/views/admin/doctors/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Details</title>
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
                    <a href="{{ route('admin.doctors.index') }}" class="active">Doctors</a>
                    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
                    <a href="{{ route('admin.specializations.index') }}">Specializations</a>
                    <a href="{{ route('admin.hospitals.index') }}">Hospitals</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <a href="{{ route('admin.doctors.index') }}" class="btn btn-sm btn-secondary mb-3">← Back</a>

                <h2 class="mb-4">{{ $doctor->first_name }} {{ $doctor->last_name }}</h2>

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
                                <h5 class="card-title">Profile Information</h5>
                                <p><strong>Email:</strong> {{ $doctor->email }}</p>
                                <p><strong>License:</strong> {{ $doctor->license_number ?? '-' }}</p>
                                <p><strong>Specializations:</strong> 
                                    @if (count($specializations) > 0)
                                        {{ implode(', ', $specializations) }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Hospital Affiliations</h5>

                                        {{-- Hospitals from junction table (approved) --}}
                                        @if ($approvedHospitals->count() > 0)
                                            <p class="mb-1"><strong>Affiliated Hospitals:</strong></p>
                                            <ul class="mb-3">
                                                @foreach ($approvedHospitals as $hospital)
                                                    <li>
                                                        {{ $hospital->hospital_name }}
                                                        @if ($hospital->city)
                                                            <span class="text-muted">({{ $hospital->city }})</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        {{-- Pending hospitals (need admin verification before doctor can be approved) --}}
                                        @if ($pendingHospitals->count() > 0)
                                            <div class="alert alert-warning">
                                                <strong>⚠️ Unverified Hospital(s) — Verify before approving doctor:</strong>
                                                <ul class="mt-2 mb-0">
                                                    @foreach ($pendingHospitals as $hospital)
                                                        <li class="d-flex align-items-center justify-content-between">
                                                            <span>{{ $hospital->hospital_name }}</span>
                                                            <form action="{{ route('admin.hospitals.verify', $hospital->hospital_id) }}" method="POST" class="ms-3">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    ✓ Add to Verified List
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- Fallback: hospital stored as plain text in doctors table --}}
                                        @if ($hospitalAffiliationText)
                                            <div class="alert alert-info">
                                                <strong>Hospital (entered during registration):</strong>
                                                {{ $hospitalAffiliationText }}
                                                <br>
                                                <small class="text-muted">
                                                    This hospital has not been added to the system list yet.
                                                    <a href="{{ route('admin.hospitals.index') }}">Add it here</a> 
                                                    before approving the doctor.
                                                </small>
                                            </div>
                                        @endif

                                        {{-- Nothing found at all --}}
                                        @if ($approvedHospitals->count() == 0 && $pendingHospitals->count() == 0 && !$hospitalAffiliationText)
                                            <p class="text-muted mb-0">No hospital specified by doctor.</p>
                                        @endif

                                    </div>
                                </div>
                                <p><strong>Experience:</strong> {{ $doctor->experience_years ?? '-' }} years</p>
                                <p><strong>City:</strong> {{ $doctor->city ?? '-' }}</p>
                                <p><strong>Consultation Fee:</strong> Rs. {{ $doctor->consultation_fee ?? '-' }}</p>
                                <p><strong>Status:</strong> 
                                    @if (!$doctor->is_verified)
                                        <span class="badge bg-warning">Pending Verification</span>
                                    @elseif ($doctor->account_status === 'suspended')
                                        <span class="badge bg-danger">Suspended</span>
                                    @else
                                        <span class="badge bg-success">Verified & Active</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Statistics</h5>
                                <p><strong>Total Appointments:</strong> {{ $appointments->total ?? 0 }}</p>
                                <p><strong>Pending:</strong> {{ $appointments->pending_count ?? 0 }}</p>
                                <p><strong>Completed:</strong> {{ $appointments->completed_count ?? 0 }}</p>
                                <p><strong>Cancelled:</strong> {{ $appointments->cancelled_count ?? 0 }}</p>
                                <p><strong>Average Rating:</strong> {{ number_format($avgRating, 1) }} ★</p>
                                <p><strong>Total Reviews:</strong> {{ $reviewCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actions</h5>
                            @if ($doctor->is_verified == 0)
                                <!-- Pending doctor - show approve button only -->
                                <form action="{{ route('admin.doctors.approve', $doctor->doctor_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>
                                <form action="{{ route('admin.doctors.destroy', $doctor->doctor_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this doctor?')">Reject/Delete</button>
                                </form>
                            @else
                                <!-- Verified doctor - show suspend/reactivate options -->
                                @if ($doctor->is_verified == 1)
                                    <form action="{{ route('admin.doctors.suspend', $doctor->doctor_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">Suspend</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.doctors.reactivate', $doctor->doctor_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-info">Reactivate</button>
                                    </form>
                            @endif
                            <form action="{{ route('admin.doctors.destroy', $doctor->doctor_id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this doctor?')">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
