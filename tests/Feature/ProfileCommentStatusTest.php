<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileCommentStatusTest extends TestCase
{
    use RefreshDatabase;

    private function reader(string $email): User
    {
        $role = Role::firstOrCreate(['slug' => Role::READER], ['name' => 'Читатель']);

        return User::create([
            'role_id' => $role->id,
            'name' => 'Читатель',
            'email' => $email,
            'password' => Hash::make('SafePassword123!'),
        ]);
    }

    public function test_reader_sees_only_own_comment_statuses_in_profile(): void
    {
        $reader = $this->reader('own@example.test');
        $other = $this->reader('other@example.test');
        $article = Article::factory()->create();

        $article->comments()->create([
            'user_id' => $reader->id,
            'body' => 'Мой ожидающий комментарий',
        ]);
        $approved = $article->comments()->create([
            'user_id' => $reader->id,
            'body' => 'Мой одобренный комментарий',
        ]);
        $approved->is_approved = true;
        $approved->save();
        $article->comments()->create([
            'user_id' => $other->id,
            'body' => 'Чужой скрытый комментарий',
        ]);

        $this->actingAs($reader)->get(route('account'))
            ->assertOk()
            ->assertSeeText('Мои комментарии')
            ->assertSeeText('На модерации')
            ->assertSeeText('Опубликован')
            ->assertSeeText('Мой ожидающий комментарий')
            ->assertSeeText('Мой одобренный комментарий')
            ->assertDontSeeText('Чужой скрытый комментарий');

        $this->get(route('news.show', $article))
            ->assertOk()
            ->assertDontSeeText('Мой ожидающий комментарий')
            ->assertDontSeeText('Чужой скрытый комментарий')
            ->assertSeeText('Мой одобренный комментарий');

        $this->actingAs($other)->get(route('account'))
            ->assertOk()
            ->assertSeeText('Чужой скрытый комментарий')
            ->assertDontSeeText('Мой ожидающий комментарий')
            ->assertDontSeeText('Мой одобренный комментарий');
    }

    public function test_notification_and_comment_paginations_are_independent(): void
    {
        $reader = $this->reader('paginated@example.test');
        $article = Article::factory()->create();

        for ($i = 1; $i <= 11; $i++) {
            $article->comments()->create([
                'user_id' => $reader->id,
                'body' => 'Мой комментарий номер '.$i,
            ]);
        }

        $this->actingAs($reader)->get(route('account', ['comments_page' => 2]))
            ->assertOk()
            ->assertSeeText('Мой комментарий номер 1')
            ->assertDontSeeText('Мой комментарий номер 11');
    }

    public function test_moderator_navigation_counts_unapproved_comments(): void
    {
        $reader = $this->reader('commenter@example.test');
        $moderatorRole = Role::firstOrCreate(
            ['slug' => Role::MODERATOR],
            ['name' => 'Модератор']
        );
        $moderator = User::create([
            'role_id' => $moderatorRole->id,
            'name' => 'Модератор',
            'email' => 'moderator-status@example.test',
            'password' => Hash::make('SafePassword123!'),
        ]);
        $article = Article::factory()->create();

        $article->comments()->create([
            'user_id' => $reader->id,
            'body' => 'Комментарий ожидает проверки',
        ]);

        $this->actingAs($moderator)->get(route('home'))
            ->assertOk()
            ->assertSee('Комментариев на модерации: 1');
        $this->actingAs($reader)->get(route('home'))
            ->assertOk()
            ->assertDontSee('Комментариев на модерации: 1');
    }
}
