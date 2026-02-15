<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function getAllBooks()
    {
        return Book::all()->latest();
    }

    /**
     * Get books with search, sort, and pagination (shared by web and API).
     */
    public function getBooksPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = Book::query()->select(['id', 'title', 'author', 'published_year', 'genre', 'created_at']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['title', 'author', 'published_year', 'created_at'];
        $sort = $request->input('sort', 'title');
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'title';
        }

        $direction = strtolower($request->input('direction', 'asc'));
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $query->orderBy($sort, $direction);

        return $query->paginate($perPage)->appends($request->query());
    }

    public function getById($id)
    {
        return Book::findOrFail($id);
    }

    public function createBook(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Book::create($data);
        });
    }

    public function updateBook($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $book = Book::findOrFail($id);
            $book->update($data);
            return $book;
        });
    }

    public function deleteBook($id)
    {
        return DB::transaction(function () use ($id) {
            $book = Book::findOrFail($id);
            $book->delete();
            return true;
        });
    }
}
