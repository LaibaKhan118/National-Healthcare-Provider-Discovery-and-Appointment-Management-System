{{-- resources/views/patient/profile/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
                    <a href="{{ route('patient.profile.edit') }}" class="active">Profile</a>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-sm btn-secondary mb-3">← Back</a>
                <h2 class="mb-4">Edit Your Profile</h2>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">Personal Information</h5>
                                <form action="{{ route('patient.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="tel" class="form-control" name="phone" value="{{ $patient->phone ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" name="date_of_birth" value="{{ $patient->date_of_birth ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="3">{{ $patient->address ?? '' }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>

                        <div class="card border-danger">
                            <div class="card-body p-4">
                                <h5 class="card-title text-danger mb-3">Danger Zone</h5>
                                <p class="text-muted mb-3">Deleting your account will permanently remove all your personal data, appointments, and reviews. This action cannot be undone.</p>
                                
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Account</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('patient.profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p><strong>Are you sure you want to delete your account?</strong></p>
                        <p class="text-muted">This will:</p>
                        <ul class="text-muted">
                            <li>Delete all your personal information</li>
                            <li>Cancel all future appointments</li>
                            <li>Delete all your reviews</li>
                        </ul>
                        <p><strong>This action cannot be undone.</strong></p>
                        
                        <div class="form-check mt-3">
                            <input type="checkbox" class="form-check-input" name="confirm_delete" value="on" id="confirmDelete" required>
                            <label class="form-check-label" for="confirmDelete">
                                I understand and want to delete my account
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete My Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
