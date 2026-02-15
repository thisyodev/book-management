<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_index_returns_paginated_payload(): void
    {
        Book::create([
            'title' => 'Alpha',
            'author' => 'Author B',
            'published_year' => 2020,
            'genre' => 'Sci-Fi',
        ]);

        Book::create([
            'title' => 'Beta',
            'author' => 'Author A',
            'published_year' => 2021,
            'genre' => 'Drama',
        ]);

        $response = $this->getJson('/api/books?sort=author&direction=asc');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);

        $authors = array_values(array_map(fn ($row) => $row['author'], $response->json('data')));

        $this->assertSame(['Author A', 'Author B'], $authors);
    }

    public function test_store_book_requires_authentication(): void
    {
        $response = $this->postJson('/api/books', [
            'title' => 'No Auth',
            'author' => 'Guest',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_update_and_delete_book(): void
    {
        $token = $this->createTokenForUser();

        $createResponse = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/books', [
                'title' => 'API Created Book',
                'author' => 'API Author',
                'published_year' => 2020,
                'genre' => 'Tech',
            ]);

        $createResponse
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'API Created Book');

        $bookId = $createResponse->json('data.id');

        $updateResponse = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/books/'.$bookId, [
                'title' => 'API Updated Book',
            ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('data.title', 'API Updated Book');

        $this->assertDatabaseHas('books', [
            'id' => $bookId,
            'title' => 'API Updated Book',
        ]);

        $deleteResponse = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/books/'.$bookId);

        $deleteResponse
            ->assertOk()
            ->assertJsonPath('status', true);

        $this->assertDatabaseMissing('books', [
            'id' => $bookId,
        ]);
    }

    private function createTokenForUser(): string
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        return auth('api')->login($user);
    }
}
