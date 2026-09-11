<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookShelfSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory()->count(5)->create();

        $genres = collect([
            '小説',
            'ビジネス',
            '技術書',
            '自己啓発',
            '歴史',
            '科学',
            '教育',
            'エッセイ',
            'ミステリー',
            'ファンタジー',
        ])->map(function ($name) {
            return Genre::create([
                'name' => $name,
            ]);
        });

        $books = Book::factory()
            ->count(11)
            ->recycle($users)
            ->create();

        foreach ($books as $book) {
            $book->genres()->attach(
                $genres->random(rand(1, 3))->pluck('id')->toArray()
            );
        }

        $reviewPairs = collect();

        while ($reviewPairs->count() < 32) {
            $user = $users->random();
            $book = $books->random();

            $key = $user->id.'-'.$book->id;

            if (! $reviewPairs->has($key)) {
                $reviewPairs->put($key, [
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                ]);
            }
        }

        $reviews = $reviewPairs->map(function ($pair) {
            return Review::factory()->create([
                'user_id' => $pair['user_id'],
                'book_id' => $pair['book_id'],
            ]);
        });

        foreach ($users as $user) {
            $favoriteBookIds = $books
                ->random(rand(3, 5))
                ->pluck('id')
                ->toArray();

            $user->favoriteBooks()->attach($favoriteBookIds);
        }

        foreach ($reviews as $review) {
            $likeCount = rand(0, 3);

            if ($likeCount === 0) {
                continue;
            }

            $likeUserIds = $users
                ->where('id', '!=', $review->user_id)
                ->random($likeCount)
                ->pluck('id')
                ->toArray();

            $review->likedByUsers()->attach($likeUserIds);
        }
    }
}
