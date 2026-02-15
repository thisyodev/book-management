<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) {}

    /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="Get list of books",
     *     tags={"Books"},
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="direction", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $ttl = (int) env('API_BOOKS_CACHE_TTL', 30);
        $version = (string) Cache::get('api_books_cache_version', 'v1');
        $cacheKey = 'api:books:' . $version . ':' . md5(http_build_query($request->query()));

        $payload = Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($request) {
            $paginator = $this->bookService->getBooksPaginated($request);

            return [
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
            ];
        });

        return response()->json($payload);
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Get book by ID",
     *     tags={"Books"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data' => $this->bookService->getById($id)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","author"},
     *             @OA\Property(property="title", type="string", example="Dune"),
     *             @OA\Property(property="author", type="string", example="Frank Herbert"),
     *             @OA\Property(property="published_year", type="integer", example=1965),
     *             @OA\Property(property="genre", type="string", example="Sci-Fi")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'genre' => 'nullable|string|max:100',
        ]);

        $book = $this->bookService->createBook($request->all());
        $this->bumpBooksCacheVersion();

        return response()->json([
            'status' => true,
            'data' => $book
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     summary="Update a book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Dune"),
     *             @OA\Property(property="author", type="string", example="Frank Herbert"),
     *             @OA\Property(property="published_year", type="integer", example=1965),
     *             @OA\Property(property="genre", type="string", example="Sci-Fi")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'genre' => 'nullable|string|max:100',
        ]);

        $book = $this->bookService->updateBook($id, $request->all());
        $this->bumpBooksCacheVersion();

        return response()->json([
            'status' => true,
            'data' => $book
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     summary="Delete a book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy($id)
    {
        $book = $this->bookService->deleteBook($id);
        $this->bumpBooksCacheVersion();

        return response()->json([
            'status' => true,
            'message' => 'Book deleted successfully'
        ]);
    }

    private function bumpBooksCacheVersion(): void
    {
        Cache::forever('api_books_cache_version', (string) microtime(true));
    }
}
