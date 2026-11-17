<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageRoutesTest extends TestCase
{
    public function test_home_page_is_available(): void
    {
        $this->get('/')->assertOk()->assertSee('Привет, я Алина');
    }

    public function test_about_page_is_available(): void
    {
        $this->get('/about')->assertOk()->assertSee('Немного обо мне');
    }

    public function test_contacts_are_rendered_from_an_array(): void
    {
        $this->get('/contacts')->assertOk()->assertSee('GitHub')->assertSee('@alinakashafutdinova');
    }
}
