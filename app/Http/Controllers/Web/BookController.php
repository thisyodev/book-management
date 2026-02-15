<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
}
