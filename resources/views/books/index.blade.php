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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
            + Add Book
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div id="alertContainer"></div>

    <!-- Search & Filter Controls -->
    <div class="card mb-4" style="border: none; background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
        <form method="GET" action="{{ route('books.index') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" value="{{ $search }}"
                    placeholder="🔍 Search by title, author, or genre..."
                    style="border-radius: 8px; border: 2px solid #e74c3c;">
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select" style="border-radius: 8px; border: 2px solid #2c3e50;"
                    onchange="this.form.submit()">
                    <option value="title" {{ $sort === 'title' ? 'selected' : '' }}>Sort by Title</option>
                    <option value="author" {{ $sort === 'author' ? 'selected' : '' }}>Sort by Author</option>
                    <option value="published_year" {{ $sort === 'published_year' ? 'selected' : '' }}>Sort by Year</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="direction" class="form-select" style="border-radius: 8px; border: 2px solid #2c3e50;"
                    onchange="this.form.submit()">
                    <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>A → Z</option>
                    <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Z → A</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Genre</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="booksTableBody">
                @forelse ($books as $key => $book)
                    <tr data-book-id="{{ $book->id }}">
                        <td>{{ $key + 1 }}</td>
                        <td class="fw-bold">{{ $book->title }}</td>
                        <td>{{ $book->author }}</td>
                        <td>{{ $book->published_year ?? '-' }}</td>
                        <td>
                            @if ($book->genre)
                                <span class="badge bg-warning text-dark">{{ $book->genre }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit"
                                data-book-id="{{ $book->id }}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                data-book-id="{{ $book->id }}" data-title="{{ $book->title }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-state-icon">📚</div>
                                <h3 class="empty-state-title">No Books Found</h3>
                                <a href="{{ route('books.index') }}" class="empty-state-link">← Clear Filters</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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

            const addModal = new bootstrap.Modal(document.getElementById('addBookModal'));
            const editModal = new bootstrap.Modal(document.getElementById('editBookModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteBookModal'));
            const saveAddBtn = document.getElementById('saveAddBtn');
            const saveEditBtn = document.getElementById('saveEditBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const alertContainer = document.getElementById('alertContainer');

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
                    const response = await fetch(`{{ url('/api/books') }}`, {
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

            // Edit book button handlers
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const bookId = this.dataset.bookId;
                    const row = document.querySelector(`tr[data-book-id="${bookId}"]`);

                    if (row) {
                        const cells = row.querySelectorAll('td');
                        document.getElementById('editBookId').value = bookId;
                        document.getElementById('editTitle').value = cells[1].textContent.trim();
                        document.getElementById('editAuthor').value = cells[2].textContent.trim();
                        document.getElementById('editYear').value = cells[3].textContent.trim() ===
                            '-' ? '' : cells[3].textContent.trim();
                        const genreCell = cells[4].textContent.trim();
                        document.getElementById('editGenre').value = genreCell === '-' ? '' :
                            genreCell;
                        editModal.show();
                    }
                });
            });

            // Save edited book — เรียกผ่าน API
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
                    const response = await fetch(`{{ url('/api/books') }}/${bookId}`, {
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

            // Delete book button handlers
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentDeleteBookId = this.dataset.bookId;
                    const title = this.dataset.title;
                    document.getElementById('deleteBookTitle').textContent = title;
                    deleteModal.show();
                });
            });

            // Confirm delete — เรียกผ่าน API
            confirmDeleteBtn.addEventListener('click', async function() {
                showSpinner();
                try {
                    const response = await fetch(`{{ url('/api/books') }}/${currentDeleteBookId}`, {
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
