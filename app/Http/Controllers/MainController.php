<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use JsonException;

class MainController extends Controller
{
    public function index(): View
    {
        return view('home', ['projects' => $this->getProjects()]);
    }

    public function gallery(int $id): View
    {
        $projects = $this->getProjects();
        $project = collect($projects)->firstWhere('id', $id);

        abort_if($project === null, 404);

        return view('galery', ['project' => $project]);
    }

    private function getProjects(): array
    {
        $path = public_path('data/projects.json');

        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        try {
            $projects = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return [];
        }

        if (!is_array($projects)) {
            return [];
        }

        return array_values(array_filter($projects, static function ($project): bool {
            return is_array($project)
                && isset($project['id'], $project['title'], $project['category'], $project['description'], $project['preview_image'], $project['full_image'])
                && is_int($project['id'])
                && is_string($project['title'])
                && is_string($project['category'])
                && is_string($project['description'])
                && is_string($project['preview_image'])
                && is_string($project['full_image']);
        }));
    }
}
