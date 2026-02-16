@extends('layouts.app')

@section('content')
    <div class="auth-shell">
        <div class="auth-panel">
            <div class="auth-card">
                <div class="auth-accent"></div>
                <h2 class="auth-title">Welcome Back</h2>
                <p class="auth-subtitle">Login with your API account to manage books.</p>

                <div id="api-error" class="alert alert-danger d-none"></div>
                <div id="api-success" class="alert alert-success d-none"></div>

                <form id="apiLoginForm" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" id="email" class="form-control auth-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" id="password" class="form-control auth-input" required>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary">Login</button>
                        <a href="{{ route('register') }}" class="btn btn-link">Create account</a>
                    </div>
                </form>

                <div class="auth-divider"></div>
                <div id="tokenInfo" class="small text-muted"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('apiLoginForm');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const errEl = document.getElementById('api-error');
            const okEl = document.getElementById('api-success');
            const tokenInfo = document.getElementById('tokenInfo');

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
                    tokenInfo.textContent = `Token expires at ${new Date(payload.exp * 1000).toLocaleString()}`;
                } else {
                    tokenInfo.textContent = 'Token stored';
                }
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                showError('');
                showSuccess('');

                try {
                    const res = await fetch('/api/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: email.value,
                            password: password.value
                        })
                    });

                    const payload = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        throw new Error(payload?.message || payload?.error || 'Login failed');
                    }

                    const token = payload.access_token || payload.token || payload.data?.token;
                    const user = payload.user || payload.data?.user || null;
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
                        } else {
                            showSuccess('Logged in successfully, but token not found in response.');
                        }
                    }

                    showSuccess('Logged in successfully. Redirecting to books...');
                    setTimeout(() => window.location.href = '/books', 900);
                } catch (err) {
                    showError(err.message || 'Login error');
                }
            });

            // show existing token info
            const existing = localStorage.getItem('api_token');
            if (existing) {
                const payload = parseJwt(existing);
                if (payload && payload.exp) {
                    tokenInfo.textContent = `Token expires at ${new Date(payload.exp * 1000).toLocaleString()}`;
                } else {
                    tokenInfo.textContent = 'Stored API token present';
                }
            }
        });
    </script>
@endsection
