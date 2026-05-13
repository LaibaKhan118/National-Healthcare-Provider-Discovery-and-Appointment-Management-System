{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Healthcare System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">Healthcare System</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Register</h3>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('register.post') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role" id="roleSelect" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>Patient</option>
                                    <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Doctor</option>
                                </select>
                            </div>

                            <div id="hospitalSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Hospital Affiliation</label>
                                    @forelse ($hospitals as $hospital)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="hospital_ids[]"
                                                value="{{ $hospital->hospital_id }}"
                                                id="reg_hospital_{{ $hospital->hospital_id }}"
                                                {{ is_array(old('hospital_ids')) && in_array($hospital->hospital_id, old('hospital_ids')) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="reg_hospital_{{ $hospital->hospital_id }}">
                                                {{ $hospital->hospital_name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-muted">No hospitals available. Contact admin.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <hr>

                        <p class="text-center mb-0">
                            Already have an account? <a href="{{ route('login') }}">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const roleSelect = document.getElementById('roleSelect');
        const hospitalSection = document.getElementById('hospitalSection');

        function toggleHospitalSection() {
            if (roleSelect.value === 'doctor') {
                hospitalSection.style.display = 'block';
            } else {
                hospitalSection.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleHospitalSection);

        // Initialize on page load
        toggleHospitalSection();
    </script>
</body>
</html>
