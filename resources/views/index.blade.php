@extends('layouts.front.layout_auth')

@section('title', 'FanZone — Login')

@section('hero')
    <h1 class="fz-auth-hero-title">
        Welcome Back to <span>FanZone</span>
    </h1>
    <p class="fz-auth-hero-sub">
        Join live match chats, answer quizzes, and stay connected with fans in real time.
    </p>
@endsection

@section('content')

<div class="fz-auth-card">

    <div class="fz-auth-card-header">
        <div class="fz-auth-card-header-icon">
            <i class="fas fa-sign-in-alt"></i>
        </div>
        <div class="fz-auth-card-title">
            Login to Your Account
        </div>
    </div>

    <div class="fz-auth-card-body">

        <form id="loginForm">
            @csrf

            <input type="hidden" name="device_id" id="device_id">

            {{-- PHONE --}}
            <div class="fz-field">
                <label class="fz-label">Phone Number</label>
                <div class="fz-input-wrap">
                    <i class="fas fa-phone fz-input-icon"></i>
                    <input
                        type="tel"
                        name="phone_number"
                        id="phone_number"
                        class="fz-input"
                        placeholder="Enter your phone number"
                        required
                    >
                </div>
            </div>

            {{-- TOKEN --}}
            <div class="fz-field">
                <label class="fz-label">Login Code (Optional)</label>
                <div class="fz-input-wrap">
                    <i class="fas fa-key fz-input-icon"></i>
                    <input
                        type="text"
                        name="token"
                        id="token"
                        class="fz-input"
                        placeholder="Required if using WiFi"
                    >
                </div>
                <div class="fz-field-hint">
                    Not subscribed? Send <strong>JOIN</strong> to *12345*
                </div>
            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="fz-btn fz-btn-primary" id="loginBtn">
                Login
            </button>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    let deviceId = localStorage.getItem('device_id');

    if (!deviceId) {
        deviceId = (crypto?.randomUUID?.()) || generateUUIDv4();
        localStorage.setItem('device_id', deviceId);
    }

    document.getElementById('device_id').value = deviceId;
})();

function generateUUIDv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

const loginForm = document.getElementById('loginForm');
const loginBtn  = document.getElementById('loginBtn');

loginForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    setLoading(true);

    const formData = new FormData(loginForm);

    try {
        const response = await fetch("{{ route('login.custom') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.redirect) {
            if (data.token) {
                localStorage.setItem('auth_token', data.token);
            }

            window.location.href = data.redirect;
            return;
        }

        if (response.status === 409 && data.requires_confirmation) {
            setLoading(false);

            Swal.fire({
                title: 'Active Session Detected',
                text: 'Continue here and log out from other device?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continue'
            }).then(result => {
                if (result.isConfirmed) {
                    submitForcedLogin();
                }
            });

            return;
        }

        throw new Error(data.message || 'Login failed');

    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Login Error',
            text: err.message
        });
    } finally {
        setLoading(false);
    }
});

async function submitForcedLogin() {
    const formData = new FormData(loginForm);
    formData.append('force', '1');

    try {
        const response = await fetch("{{ route('login.custom') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (data.token) {
            localStorage.setItem('auth_token', data.token);
        }

        window.location.href = data.redirect;

    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Login Error',
            text: err.message
        });
    }
}

function setLoading(state) {
    loginBtn.disabled = state;
    loginBtn.innerText = state ? 'Logging in…' : 'Login';
}
</script>
@endpush