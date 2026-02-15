@extends('layouts.app')

@section('content')
    <div class="auth-shell">
        <div class="auth-panel">
            <div class="auth-card">
                <div class="auth-accent"></div>
                <h2 class="auth-title">Create Your Account</h2>
                <p class="auth-subtitle">Register via the API to start managing books.</p>

                <div id="api-error" class="alert alert-danger d-none"></div>
                <div id="api-success" class="alert alert-success d-none"></div>

                <form id="apiRegisterForm" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" id="name" class="form-control auth-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" id="email" class="form-control auth-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" id="password" class="form-control auth-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" id="passwordConfirmation" class="form-control auth-input" required>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                        <a href="{{ route('login') }}" class="btn btn-link">Already have an account?</a>
                    </div>
                </form>

                <div class="auth-divider"></div>
                <div class="small text-muted">By registering, you will receive an API token for this session.</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('apiRegisterForm');
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('passwordConfirmation');
            const errEl = document.getElementById('api-error');
            const okEl = document.getElementById('api-success');

            function showError(msg) {
                errEl.textContent = msg;
                errEl.classList.remove('d-none');
                okEl.classList.add('d-none');
            }

            function showSuccess(msg) {
                okEl.textContent = msg;
                okEl.classList.remove('d-none');
                errEl.classList.add('d-none');
            }

            function parseJwt(token) {
                try {
                    const p = token.split('.')[1];
                    return JSON.parse(atob(p.replace(/-/g, '+').replace(/_/g, '/')));
                } catch (e) {
                    return null;
                }
            }

            function setToken(token) {
                localStorage.setItem('api_token', token);
                const payload = parseJwt(token);
                if (payload && payload.exp) {
                    showSuccess(
                        `Registered successfully. Token expires at ${new Date(payload.exp * 1000).toLocaleString()}`
                    );
                }
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                showError('');
                showSuccess('');

                if (password.value !== passwordConfirmation.value) {
                    showError('Passwords do not match.');
                    return;
                }

                try {
                    const res = await fetch('/api/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name.value,
                            email: email.value,
                            password: password.value
                        })
                    });

                    const payload = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        throw new Error(payload?.message || payload?.error || 'Registration failed');
                    }

                    const token = payload.access_token || payload.token || payload.data?.token;
                    const user = payload.user || payload.data?.user || {
                        name: name.value,
                        email: email.value
                    };
                    if (user) {
                        localStorage.setItem('api_user', JSON.stringify(user));
                    }
                    if (token) {
                        setToken(token);
                    } else {
                        const header = res.headers.get('Authorization') || res.headers.get(
                            'authorization');
                        if (header && header.startsWith('Bearer ')) {
                            setToken(header.split(' ')[1]);
                        }
                    }

                    showSuccess('Registered successfully. Redirecting to books...');
                    setTimeout(() => window.location.href = '/book', 900);
                } catch (err) {
                    showError(err.message || 'Registration error');
                }
            });
        });
    </script>
@endsection
