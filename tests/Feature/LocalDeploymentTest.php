<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use App\Notifications\CommentModeratedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LocalDeploymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_database_supports_all_tables_including_notifications(): void
    {
        foreach (['roles', 'users', 'articles', 'comments', 'personal_access_tokens', 'notifications'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
        $this->assertTrue(Schema::hasColumn('notifications', 'read_at'));
    }

    public function test_user_can_see_and_mark_moderation_notification_as_read(): void
    {
        $role = Role::firstOrCreate(['slug' => Role::READER], ['name' => 'Читатель']);
        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Тестовый читатель',
            'email' => 'local-reader@example.test',
            'password' => Hash::make('LocalPassword123!'),
        ]);
        $article = Article::factory()->create();
        $user->notify(new CommentModeratedNotification(true, $article->id, $article->title, 'Хорошая статья'));

        $this->actingAs($user)->get(route('account'))
            ->assertOk()->assertSeeText('Комментарий опубликован')->assertSeeText($article->title);

        $this->assertSame(1, $user->unreadNotifications()->count());
        $this->post(route('account.notifications.read'))->assertRedirect(route('account'));
        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_comment_editing_routes_are_not_exposed(): void
    {
        foreach (['comments.edit', 'comments.update', 'comments.destroy'] as $route) {
            $this->assertNull(Route::getRoutes()->getByName($route));
        }
    }
}
