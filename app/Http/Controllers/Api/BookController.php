<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) {}

/**
 * @OA\Get(
 *     path="/api/v1/books",
 *     summary="Get list of books",
 *     tags={"Books"},
 *     @OA\Parameter(name="search", in="query", required=false),
 *     @OA\Parameter(name="sort", in="query", required=false),
 *     @OA\Parameter(name="direction", in="query", required=false),
 *     @OA\Parameter(name="page", in="query", required=false),
 *     @OA\Response(
 *         response=200,
 *         description="Success"
 *     )
 * )
 */
    public function index(Request $request)
    {
        $paginator = $this->bookService->getBooksPaginated($request);
        return response()->json([
            'status' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data' => $this->bookService->getById($id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'genre' => 'nullable|string|max:100',
        ]);

        $book = $this->bookService->createBook($request->all());

        return response()->json([
            'status' => true,
            'data' => $book
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'genre' => 'nullable|string|max:100',
        ]);

        $book = $this->bookService->updateBook($id, $request->all());

        return response()->json([
            'status' => true,
            'data' => $book
        ]);
    }

    public function destroy($id)
    {
        $book = $this->bookService->deleteBook($id);

        return response()->json([
            'status' => true,
            'message' => 'Book deleted successfully'
        ]);
    }
}
