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
        foreach (['articles', 'users', 'roles', 'comments', 'personal_access_tokens', 'notifications'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
        $this->assertTrue(Schema::hasColumn('comments', 'is_approved'));
    }

    public function test_infinityfree_root_entrypoint_exists_and_is_safe(): void
    {
        $this->assertFileExists(base_path('index.php'));
        $entrypoint = file_get_contents(base_path('index.php'));
        $this->assertStringContainsString("require __DIR__.'/public/index.php';", $entrypoint);

        $this->assertFileExists(base_path('.htaccess'));
        $rewrite = file_get_contents(base_path('.htaccess'));
        $this->assertStringContainsString('RewriteEngine On', $rewrite);
        $this->assertStringContainsString('index.php', $rewrite);
        $this->assertStringContainsString('public/', $rewrite);
        $this->assertStringContainsString('storage|tests|vendor', $rewrite);
    }

    public function test_seeding_does_not_duplicate_articles(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(12, Article::count());
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(12, Article::count());
    }
}
