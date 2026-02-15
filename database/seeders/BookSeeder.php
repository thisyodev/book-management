<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'published_year' => 1925,
                'genre' => 'Fiction',
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'published_year' => 1960,
                'genre' => 'Fiction',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'published_year' => 1949,
                'genre' => 'Dystopian',
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'published_year' => 1813,
                'genre' => 'Romance',
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'published_year' => 1951,
                'genre' => 'Fiction',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'published_year' => 2008,
                'genre' => 'Programming',
            ],
            [
                'title' => 'Design Patterns',
                'author' => 'Gang of Four',
                'published_year' => 1994,
                'genre' => 'Software Engineering',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt, David Thomas',
                'published_year' => 1999,
                'genre' => 'Programming',
            ],
            [
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'published_year' => 2011,
                'genre' => 'History',
            ],
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'published_year' => 2018,
                'genre' => 'Self-Help',
            ],
            [
                'title' => 'The Power of Now',
                'author' => 'Eckhart Tolle',
                'published_year' => 1997,
                'genre' => 'Philosophy',
            ],
            [
                'title' => 'Educated',
                'author' => 'Tara Westover',
                'published_year' => 2018,
                'genre' => 'Memoir',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
