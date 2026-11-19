<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HostingConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mysql_compatible_tables_and_foreign_keys(): void
    {
        foreach (['articles', 'users', 'roles', 'comments', 'personal_access_tokens'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
        $this->assertTrue(Schema::hasColumn('comments', 'is_approved'));
    }

    public function test_seeding_does_not_duplicate_articles(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(12, Article::count());
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(12, Article::count());
    }
}
