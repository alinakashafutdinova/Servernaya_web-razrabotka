<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    private function validData(): array
    {
        return [
            'name' => 'Алина',
            'email' => 'alina@example.com',
            'password' => 'SafePass123!',
        ];
    }

    public function test_form_opens_at_signin(): void
    {
        $this->get('/signin')
            ->assertOk()
            ->assertViewIs('auth.signin')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('method="post"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_post_route_has_web_middleware_for_csrf(): void
    {
        $route = Route::getRoutes()->getByName('signin.registration');
        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
        $this->assertContains('web', $route->gatherMiddleware());
    }

    public function test_valid_registration_returns_json_without_exposing_password(): void
    {
        $response = $this->post('/signin', $this->validData());

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonPath('user.name', 'Алина')
            ->assertJsonPath('user.email', 'alina@example.com')
            ->assertJsonPath('user.password', '[скрыт]')
            ->assertDontSee('SafePass123!');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_empty_fields_are_rejected(): void
    {
        $this->postJson('/signin', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $data = $this->validData();
        $data['email'] = 'not-an-email';
        $this->postJson('/signin', $data)->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_short_password_is_rejected(): void
    {
        $data = $this->validData();
        $data['password'] = '1234567';
        $this->postJson('/signin', $data)->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_name_longer_than_80_characters_is_rejected(): void
    {
        $data = $this->validData();
        $data['name'] = str_repeat('a', 81);
        $this->postJson('/signin', $data)->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_long_password_is_rejected(): void
    {
        $data = $this->validData();
        $data['password'] = str_repeat('x', 73);
        $this->postJson('/signin', $data)->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_invalid_html_form_redirects_back_with_errors(): void
    {
        $this->from('/signin')->post('/signin', ['name' => '', 'email' => 'bad', 'password' => 'x'])
            ->assertRedirect('/signin')->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_requested_text_changes_are_visible(): void
    {
        $this->get('/')->assertOk()->assertSeeText('Алина Кашафутдинова')->assertDontSee('Студентка · Веб-технологии · Москва');
        $this->get('/about')->assertOk()->assertSeeText('лабораторная работа')->assertDontSee('моя первая лабораторная работа');
    }

    public function test_navigation_contains_registration_link_and_other_pages_still_work(): void
    {
        $this->get('/')->assertOk()->assertSee('/register');
        $this->get('/about')->assertOk();
        $this->get('/contacts')->assertOk();
        $this->get('/galery/1')->assertOk();
    }
}
