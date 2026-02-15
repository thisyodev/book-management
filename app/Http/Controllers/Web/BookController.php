<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) {}

    public function index(Request $request)
    {
        $dbError = null;

        try {
            $books = $this->bookService->getBooksPaginated($request);
        } catch (Throwable $exception) {
            Log::channel('error_daily')->error('web.books.index_failed', [
                'message' => $exception->getMessage(),
                'path' => $request->path(),
            ]);

            $dbError = 'Database is not available right now. Please try again later.';
            $books = new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: 10,
                currentPage: 1,
                options: [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return view('books.index', [
            'books' => $books,
            'search' => $request->input('search', ''),
            'sort' => $request->input('sort', 'title'),
            'direction' => $request->input('direction', 'asc'),
            'dbError' => $dbError,
        ]);
    }
}
