<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function create(User $user, Article $article): Response
    {
        return $article->is_published
            ? Response::allow()
            : Response::denyAsNotFound('Нельзя комментировать неопубликованную статью.');
    }

    public function update(User $user, Comment $comment): Response
    {
        return Response::deny('Изменять комментарии может только модератор.');
    }

    public function delete(User $user, Comment $comment): Response
    {
        return Response::deny('Удалять комментарии может только модератор.');
    }
}
