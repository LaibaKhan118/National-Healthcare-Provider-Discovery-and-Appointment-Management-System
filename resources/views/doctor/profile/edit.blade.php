{{-- resources/views/doctor/profile/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
                    <a href="{{ route('doctor.availability.index') }}">Availability</a>
                    <a href="{{ route('doctor.profile.edit') }}" class="active">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-sm btn-secondary mb-3">← Back</a>
                <h2 class="mb-4">Edit Your Profile</h2>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('doctor.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="{{ $user->first_name }}" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" value="{{ $user->last_name }}" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">License Number</label>
                                    <input type="text" class="form-control" name="license_number" value="{{ $doctor->license_number ?? '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Experience (Years)</label>
                                    <input type="number" class="form-control" name="experience_years" value="{{ $doctor->experience_years ?? '' }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="city" value="{{ $doctor->city ?? '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Consultation Fee (Rs.)</label>
                                    <input type="number" class="form-control" name="consultation_fee" step="0.01" value="{{ $doctor->consultation_fee ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hospitals</label>
                                @forelse ($hospitals as $hospital)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="hospital_ids[]"
                                            value="{{ $hospital->hospital_id }}"
                                            id="hospital_{{ $hospital->hospital_id }}"
                                            {{ in_array($hospital->hospital_id, $doctorHospitals) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="hospital_{{ $hospital->hospital_id }}">
                                            {{ $hospital->hospital_name }}
                                            {!! $hospital->is_pending_verification ? '<span class="badge bg-warning">Pending</span>' : '' !!}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">No hospitals available. Contact admin.</p>
                                @endforelse
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Specializations</label>
                                @foreach ($specializations as $spec)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="specialization_ids[]"
                                            value="{{ $spec->specialization_id }}"
                                            id="spec_{{ $spec->specialization_id }}"
                                            {{ in_array($spec->specialization_id, $doctorSpecs) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="spec_{{ $spec->specialization_id }}">
                                            {{ $spec->specialization_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea class="form-control" name="bio" rows="4" maxlength="1000">{{ $doctor->bio ?? '' }}</textarea>
                                <small class="text-muted">Max 1000 characters</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
