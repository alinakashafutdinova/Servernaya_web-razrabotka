<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProjectScopeTest extends TestCase
{
    public function test_project_scope_is_shown_on_main_pages(): void
    {
        $this->get('/')->assertOk()->assertSeeText('ЛАБОРАТОРНЫЕ РАБОТЫ №1–9');
        $this->get('/about')->assertOk()
            ->assertSeeText('Этот проект объединяет лабораторные работы №1–9')
            ->assertSeeText('Московском Политехническом Университете');
    }

    public function test_auth_forms_and_footer_use_all_nine_labs(): void
    {
        foreach (['/login', '/register', '/signin'] as $url) {
            $this->get($url)->assertOk()->assertSeeText('ЛАБОРАТОРНЫЕ РАБОТЫ №1–9')
                ->assertSeeText('Лабораторные работы №1–9');
        }
    }
}
