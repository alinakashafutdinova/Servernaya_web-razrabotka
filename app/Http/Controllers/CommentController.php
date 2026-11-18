<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function store(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('create', [Comment::class, $article]);
        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'body.required' => 'Напиши текст комментария.',
            'body.min' => 'Комментарий должен содержать не менее 3 символов.',
            'body.max' => 'Комментарий не должен превышать 1000 символов.',
        ]);
        $article->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('news.show', $article)->with('status', 'Комментарий добавлен.');
    }

    public function edit(Comment $comment): View
    {
        $this->authorize('update', $comment);
        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);
        $data = $request->validate(['body' => ['required', 'string', 'min:3', 'max:1000']]);
        $comment->update($data);
        return redirect()->route('news.show', $comment->article)->with('status', 'Комментарий изменён.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);
        $article = $comment->article;
        $comment->delete();
        return redirect()->route('news.show', $article)->with('status', 'Комментарий удалён.');
    }
}
