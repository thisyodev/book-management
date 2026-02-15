@extends('layouts.app')

@section('content')
    <!-- Modern Loading Spinner -->
    <div id="loadingSpinner" class="d-none"
        style="
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    ">
        <div style="text-align: center; color: white;">
            <div
                style="
                width: 80px;
                height: 80px;
                margin: 0 auto 20px;
                border: 6px solid rgba(255, 255, 255, 0.2);
                border-top: 6px solid #fff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            ">
            </div>
            <p style="font-size: 1.1rem; font-weight: 500;">Loading...</p>
        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Books Library</h2>
        <button type="button" class="btn btn-primary {{ auth()->check() ? '' : 'd-none' }}" data-auth-only
            data-bs-toggle="modal" data-bs-target="#addBookModal">
            + Add Book
        </button>
        <a class="btn btn-primary {{ auth()->check() ? 'd-none' : '' }}" href="{{ route('login', absolute: false) }}"
            data-guest-only>Login
            to Add</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (!empty($dbError))
        <div class="alert alert-danger" role="alert">
            {{ $dbError }}
        </div>
    @endif

    <div id="alertContainer"></div>

    <!-- Search & Filter Controls -->
    <div class="search-filter-panel mb-4">
        <form method="GET" action="{{ route('books.index') }}" class="search-filter-form">
            <div class="search-block">
                <label class="filter-label-inline" for="searchInput">Search</label>
                <div class="search-field">
                    <span class="search-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"></circle>
                            <path d="M20 20L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            </path>
                        </svg>
                    </span>
                    <input type="text" id="searchInput" name="search" class="form-control search-input"
                        value="{{ $search }}" placeholder="Search by title, author, or genre...">
                </div>
            </div>
            <div class="filter-actions">
                <div class="filter-inline">
                    <label class="filter-label-inline" for="sortSelect">Sort</label>
                    <select id="sortSelect" name="sort" class="form-select filter-select-inline"
                        onchange="this.form.submit()">
                        <option value="title" {{ $sort === 'title' ? 'selected' : '' }}>Title</option>
                        <option value="author" {{ $sort === 'author' ? 'selected' : '' }}>Author</option>
                        <option value="published_year" {{ $sort === 'published_year' ? 'selected' : '' }}>Year</option>
                    </select>
                </div>
                <div class="filter-inline">
                    <label class="filter-label-inline" for="directionSelect">Order</label>
                    <select id="directionSelect" name="direction" class="form-select filter-select-inline"
                        onchange="this.form.submit()">
                        <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>A → Z</option>
                        <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Z → A</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary filter-submit">Apply</button>
            </div>
        </form>
    </div>

    <div class="book-cards-container" id="booksTableBody">
        @forelse ($books as $key => $book)
            <div class="book-card" data-book-id="{{ $book->id }}" data-title="{{ $book->title }}"
                data-author="{{ $book->author }}" data-year="{{ $book->published_year ?? '' }}"
                data-genre="{{ $book->genre ?? '' }}">
                <div class="book-card-body">
                    <div class="book-title">{{ $book->title }}</div>
                    <div class="book-author">{{ $book->author }}</div>
                    <div class="book-meta">
                        <span>Year</span>
                        <span>{{ $book->published_year ?? '-' }}</span>
                    </div>
                    <div class="book-meta">
                        <span>Genre</span>
                        <span>
                            @if ($book->genre)
                                <span class="badge bg-warning text-dark">{{ $book->genre }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="mt-3 d-flex gap-2 d-none" data-auth-only>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit" data-auth-only
                            data-book-id="{{ $book->id }}">
                            Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-auth-only
                            data-book-id="{{ $book->id }}" data-title="{{ $book->title }}">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state w-100 text-center py-5">
                <div class="empty-state-icon">📚</div>
                <h3 class="empty-state-title">No Books Found</h3>
                <a href="{{ route('books.index') }}" class="empty-state-link">← Clear Filters</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <nav class="mt-5">
        {{ $books->links('pagination::bootstrap-5') }}
    </nav>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBookForm">
                        <div class="mb-3">
                            <label for="addTitle" class="form-label">Title</label>
                            <input type="text" id="addTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAuthor" class="form-label">Author</label>
                            <input type="text" id="addAuthor" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="addYear" class="form-label">Published Year</label>
                            <input type="number" id="addYear" class="form-control" min="1000">
                        </div>
                        <div class="mb-3">
                            <label for="addGenre" class="form-label">Genre</label>
                            <input type="text" id="addGenre" class="form-control" placeholder="e.g., Fiction">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveAddBtn">Add Book</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBookForm">
                        <input type="hidden" id="editBookId">
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Title</label>
                            <input type="text" id="editTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAuthor" class="form-label">Author</label>
                            <input type="text" id="editAuthor" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editYear" class="form-label">Published Year</label>
                            <input type="number" id="editYear" class="form-control" min="1000">
                        </div>
                        <div class="mb-3">
                            <label for="editGenre" class="form-label">Genre</label>
                            <input type="text" id="editGenre" class="form-control" placeholder="e.g., Fiction">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                <div class="modal-header" style="border: none;">
                    <h5 class="modal-title">Delete Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                    <h6>Are you sure you want to delete this book?</h6>
                    <p id="deleteBookTitle" style="font-weight: bold; color: #e74c3c; margin-top: 0.5rem;"></p>
                    <p style="color: #666; font-size: 0.9rem;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer" style="border: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const loadingSpinner = document.getElementById('loadingSpinner');
        let currentDeleteBookId = null;

        function showSpinner() {
            loadingSpinner.classList.remove('d-none');
        }

        function hideSpinner() {
            loadingSpinner.classList.add('d-none');
        }

        document.addEventListener('DOMContentLoaded', function() {
            hideSpinner();
            const hasWebAuth = @json(auth()->check());
            const apiToken = localStorage.getItem('api_token');

            function setAuthUi(isAuthed) {
                document.querySelectorAll('[data-auth-only]').forEach(el => {
                    el.classList.toggle('d-none', !isAuthed);
                });
                document.querySelectorAll('[data-guest-only]').forEach(el => {
                    el.classList.toggle('d-none', isAuthed);
                });
            }

            setAuthUi(hasWebAuth || Boolean(apiToken));
            if (apiToken) {
                fetch('/api/me', {
                    headers: {
                        'Authorization': 'Bearer ' + apiToken
                    }
                }).then(res => {
                    if (!res.ok) {
                        if (res.status === 401) {
                            localStorage.removeItem('api_token');
                            localStorage.removeItem('api_user');
                        }
                        setAuthUi(false);
                        return;
                    }
                    setAuthUi(true);
                }).catch(() => {
                    setAuthUi(hasWebAuth);
                });
            } else {
                setAuthUi(hasWebAuth);
            }

            const addModalEl = document.getElementById('addBookModal');
            const editModalEl = document.getElementById('editBookModal');
            const deleteModalEl = document.getElementById('deleteBookModal');
            const addModal = addModalEl ? new bootstrap.Modal(addModalEl) : null;
            const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
            const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
            const saveAddBtn = document.getElementById('saveAddBtn');
            const saveEditBtn = document.getElementById('saveEditBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const alertContainer = document.getElementById('alertContainer');
            const apiBooksBase = '/api/books';

            function getApiHeaders(method, body = null) {
                const headers = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };
                const token = localStorage.getItem('api_token');
                if (token) headers['Authorization'] = 'Bearer ' + token;
                return headers;
            }

            // Add book button handler — เรียกผ่าน API
            if (saveAddBtn) {
                saveAddBtn.addEventListener('click', async function() {
                    const data = {
                        title: document.getElementById('addTitle').value,
                        author: document.getElementById('addAuthor').value,
                        published_year: document.getElementById('addYear').value || null,
                        genre: document.getElementById('addGenre').value || null,
                    };

                    if (!data.title || !data.author) {
                        showAlert('Title and Author are required!', 'warning');
                        return;
                    }

                    showSpinner();
                    try {
                        const response = await fetch(apiBooksBase, {
                            method: 'POST',
                            headers: getApiHeaders('POST'),
                            body: JSON.stringify(data)
                        });

                        const json = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            if (response.status === 401) {
                                throw new Error('Please log in to add books.');
                            }
                            throw new Error(json.message || 'Failed to add book');
                        }

                        showAlert('Book added successfully!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } catch (error) {
                        hideSpinner();
                        showAlert('Error: ' + error.message, 'danger');
                    }
                });
            }

            // Edit book button handlers
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const bookId = this.dataset.bookId;
                    const card = document.querySelector(`.book-card[data-book-id="${bookId}"]`);

                    if (card) {
                        document.getElementById('editBookId').value = bookId;
                        document.getElementById('editTitle').value = card.dataset.title || '';
                        document.getElementById('editAuthor').value = card.dataset.author || '';
                        document.getElementById('editYear').value = card.dataset.year || '';
                        document.getElementById('editGenre').value = card.dataset.genre || '';
                        if (editModal) {
                            editModal.show();
                        }
                    }
                });
            });

            // Save edited book — เรียกผ่าน API
            if (saveEditBtn) {
                saveEditBtn.addEventListener('click', async function() {
                    const bookId = document.getElementById('editBookId').value;
                    const data = {
                        title: document.getElementById('editTitle').value,
                        author: document.getElementById('editAuthor').value,
                        published_year: document.getElementById('editYear').value || null,
                        genre: document.getElementById('editGenre').value || null,
                    };

                    if (!data.title || !data.author) {
                        showAlert('Title and Author are required!', 'warning');
                        return;
                    }

                    showSpinner();
                    try {
                        const response = await fetch(`${apiBooksBase}/${bookId}`, {
                            method: 'PUT',
                            headers: getApiHeaders('PUT'),
                            body: JSON.stringify(data)
                        });

                        const json = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            if (response.status === 401) {
                                throw new Error('Please log in to update books.');
                            }
                            throw new Error(json.message || 'Failed to update book');
                        }

                        showAlert('Book updated successfully!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } catch (error) {
                        hideSpinner();
                        showAlert('Error: ' + error.message, 'danger');
                    }
                });
            }

            // Delete book button handlers
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentDeleteBookId = this.dataset.bookId;
                    const title = this.dataset.title;
                    document.getElementById('deleteBookTitle').textContent = title;
                    if (deleteModal) {
                        deleteModal.show();
                    }
                });
            });

            // Confirm delete — เรียกผ่าน API
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', async function() {
                    showSpinner();
                    try {
                        const response = await fetch(
                            `${apiBooksBase}/${currentDeleteBookId}`, {
                                method: 'DELETE',
                                headers: getApiHeaders('DELETE')
                            });

                        const json = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            if (response.status === 401) {
                                throw new Error('Please log in to delete books.');
                            }
                            throw new Error(json.message || 'Failed to delete book');
                        }

                        showAlert('Book deleted successfully!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } catch (error) {
                        hideSpinner();
                        showAlert('Error: ' + error.message, 'danger');
                    }
                });
            }

            function showAlert(message, type) {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show`;
                alert.role = 'alert';
                alert.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                alertContainer.innerHTML = '';
                alertContainer.appendChild(alert);
            }
        });
    </script>
@endsection
