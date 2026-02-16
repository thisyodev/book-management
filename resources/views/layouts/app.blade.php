<!DOCTYPE html>
<html>

<head>
    <title>Book App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Bookstore CSS -->
    <link rel="stylesheet" href="{{ asset('css/bookstore.css') }}">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand">Book App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name ?? Auth::user()->email }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>
    @yield('scripts')

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // If user logged in via API token (localStorage), fetch profile and update navbar
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('api_token');
            if (!token) return;

            const navList = document.querySelector('.navbar-nav.ms-auto');
            if (!navList) return;

            const cachedUser = (() => {
                try {
                    return JSON.parse(localStorage.getItem('api_user') || 'null');
                } catch (err) {
                    return null;
                }
            })();

            function renderUserNav(user) {
                if (!user) return;
                navList.innerHTML = `
                    <li class="nav-item dropdown">
                        <a id="clientNavbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ${user.name ? escapeHtml(user.name) : escapeHtml(user.email)}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="clientNavbarDropdown">
                            <li><a class="dropdown-item" href="/books">Books</a></li>
                            <li><a class="dropdown-item" href="#" id="clientLogout">Logout</a></li>
                        </ul>
                    </li>
                `;

                const logoutBtn = document.getElementById('clientLogout');
                if (logoutBtn) {
                    logoutBtn.addEventListener('click', async function(e) {
                        e.preventDefault();
                        try {
                            await fetch('/api/logout', {
                                method: 'POST',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                }
                            }).catch(() => {});
                        } catch (err) {}
                        localStorage.removeItem('api_token');
                        localStorage.removeItem('api_user');
                        window.location.href = '/books';
                    });
                }
            }

            if (cachedUser) {
                renderUserNav(cachedUser);
            }

            fetch('/api/me', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            }).then(async res => {
                if (!res.ok) {
                    if (res.status === 401) {
                        localStorage.removeItem('api_token');
                        localStorage.removeItem('api_user');
                    }
                    return;
                }
                const user = await res.json().catch(() => null);
                if (!user) return;
                localStorage.setItem('api_user', JSON.stringify(user));
                renderUserNav(user);
            }).catch(() => {
                localStorage.removeItem('api_token');
                localStorage.removeItem('api_user');
            });

            function escapeHtml(unsafe) {
                return String(unsafe)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        });
    </script>

</body>

</html>
