<?php

namespace Tests\Feature;

use Tests\TestCase;

class FormalLanguageTest extends TestCase
{
    public function test_home_and_about_have_formal_wording(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeText('Здравствуйте!')->assertSeeText('Я Алина')
            ->assertSeeText('Нажмите на превью');

        $this->get('/about')
            ->assertOk()
            ->assertSeeText('Я Алина, учусь на направлении «Веб-технологии» в Московском Политехническом Университете.');
    }

    public function test_auth_forms_address_visitors_formally(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSeeText('Войдите в аккаунт')
            ->assertSee('Ваш пароль')
            ->assertDontSee('Твой пароль');

        $this->get('/register')
            ->assertOk()
            ->assertSeeText('Создайте аккаунт')
            ->assertSee('Ваше имя')
            ->assertDontSee('Как тебя зовут');
    }
}
