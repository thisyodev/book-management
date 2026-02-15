@extends('layouts.app')

@section('content')
    <h2 style="margin-bottom: 2rem;">Edit Book</h2>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert"
            style="max-width: 500px; margin: 0 auto 2rem;">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('books.update', $book) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $book->title) }}" required autofocus>
            @error('title')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="author" class="form-label">Author</label>
            <input type="text" id="author" name="author" class="form-control @error('author') is-invalid @enderror"
                value="{{ old('author', $book->author) }}" required>
            @error('author')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="published_year" class="form-label optional">Published Year</label>
            <input type="number" id="published_year" name="published_year" class="form-control"
                value="{{ old('published_year', $book->published_year) }}" min="1000">
        </div>

        <div class="mb-3">
            <label for="genre" class="form-label optional">Genre</label>
            <input type="text" id="genre" name="genre" class="form-control"
                value="{{ old('genre', $book->genre) }}" placeholder="e.g., Fiction, Science, History">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Book</button>
            <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
