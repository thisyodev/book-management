<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) {}

    public function index(Request $request)
    {
        $books = $this->bookService->getBooksPaginated($request);
        return view('books.index', [
            'books' => $books,
            'search' => $request->input('search', ''),
            'sort' => $request->input('sort', 'title'),
            'direction' => $request->input('direction', 'asc'),
        ]);
    }

    // public function create()
    // {
    //     return view('books.create');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'author' => 'required|string|max:255',
    //         'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
    //         'genre' => 'nullable|string|max:100',
    //     ]);

    //     $book = $this->bookService->createBook($request->all());

    //     if ($request->expectsJson() || $request->header('Accept') === 'application/json' || $request->isXmlHttpRequest()) {
    //         return response()->json(['message' => 'Book created successfully', 'book' => $book], 201);
    //     }

    //     return redirect()->route('books.index')->with('success', 'Book created successfully');
    // }

    // public function edit(Book $book)
    // {
    //     return view('books.edit', ['book' => $book]);
    // }

    // public function update(Request $request, Book $book)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'author' => 'required|string|max:255',
    //         'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
    //         'genre' => 'nullable|string|max:100',
    //     ]);

    //     dd($request->all(), $book);

    //     $book = $this->bookService->updateBook($book->id, $request->all());

    //     if ($request->expectsJson() || $request->header('Accept') === 'application/json' || $request->isXmlHttpRequest()) {
    //         return response()->json(['message' => 'Book updated successfully', 'book' => $book]);
    //     }

    //     return redirect()->route('books.index')->with('success', 'Book updated successfully');
    // }

    // public function destroy(Request $request, Book $book)
    // {
    //     $this->bookService->deleteBook($book->id);

    //     if ($request->expectsJson() || $request->header('Accept') === 'application/json' || $request->isXmlHttpRequest()) {
    //         return response()->json(['message' => 'Book deleted successfully'], 200);
    //     }

    //     return redirect()->route('books.index')->with('success', 'Book deleted successfully');
    // }
}
