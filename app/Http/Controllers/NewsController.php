<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('news.index', ['articles' => $articles]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published, 404);

        return view('news.show', ['article' => $article]);
    }
}
