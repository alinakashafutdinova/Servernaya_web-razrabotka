<?php

namespace Tests\Feature;

use Tests\TestCase;

class JsonGalleryTest extends TestCase
{
    public function test_home_page_displays_all_cards_from_json(): void
    {
        $projects = json_decode(file_get_contents(public_path('data/projects.json')), true);

        $response = $this->get('/');
        $response->assertOk()->assertViewIs('home')->assertViewHas('projects');

        foreach ($projects as $project) {
            $response->assertSee($project['title']);
            $response->assertSee('/galery/'.$project['id']);
            $response->assertSee('images/gallery/preview-'.$project['id'].'.svg');
        }
    }

    public function test_gallery_shows_correct_full_image(): void
    {
        $this->get('/galery/3')
            ->assertOk()
            ->assertViewIs('galery')
            ->assertSee('Типографика')
            ->assertSee('images/gallery/full-3.svg');
    }

    public function test_gallery_returns_not_found_for_unknown_id(): void
    {
        $this->get('/galery/999')->assertNotFound();
    }

    public function test_gallery_rejects_non_numeric_id(): void
    {
        $this->get('/galery/not-a-number')->assertNotFound();
    }

    public function test_other_pages_are_not_affected(): void
    {
        $this->get('/about')->assertOk();
        $this->get('/contacts')->assertOk()->assertSee('@alinakashafutdinova');
    }
}
