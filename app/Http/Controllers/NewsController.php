<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(6);

        return view('news.index', ['articles' => $articles]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published, 404);

        $relatedArticles = Article::query()
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('news.show', compact('article', 'relatedArticles'));
    }

    public function manage(): View
    {
        $articles = Article::query()->orderByDesc('id')->paginate(8);

        return view('news.manage', compact('articles'));
    }

    public function create(): View
    {
        return view('news.create');
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        Article::create($data);

        return redirect()->route('news.manage')->with('status', 'Статья успешно добавлена.');
    }

    public function edit(Article $article): View
    {
        return view('news.edit', compact('article'));
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $article->update($data);

        return redirect()->route('news.manage')->with('status', 'Изменения сохранены.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('news.manage')->with('status', 'Статья удалена.');
    }
}
