@extends('layouts.app')

@section('title', 'Мой профиль и уведомления')

@section('content')
<section class="account-dashboard container">
    <div class="profile-header">
        <span class="eyebrow"><span class="eyebrow-dot"></span> ВЫ В СВОЁМ АККАУНТЕ</span>
        <h1>Добро пожаловать, <span class="serif-accent">{{ auth()->user()->name }}!</span></h1>
        <p>Следите за статусом своих комментариев и решениями модератора.</p>
    </div>
    <div class="profile-summary">
        <div><span>ПРОФИЛЬ</span><strong>{{ auth()->user()->name }}</strong></div>
        <div><span>ЭЛЕКТРОННАЯ ПОЧТА</span><strong>{{ auth()->user()->email }}</strong></div>
        <div><span>РОЛЬ</span><strong>{{ auth()->user()->isModerator() ? 'Модератор' : 'Читатель' }}</strong></div>
    </div>

    <section class="profile-comments" aria-labelledby="profile-comments-title">
        <div class="profile-notifications-head">
            <div>
                <h2 id="profile-comments-title">Мои <span class="serif-accent">комментарии.</span></h2>
                <p>Ожидающие проверки видны только вам и модератору. Одобренные появляются под статьями.</p>
            </div>
        </div>
        <div class="profile-notifications-list">
            @forelse ($comments as $comment)
                <article class="profile-notification">
                    <div class="profile-notification-top">
                        <span class="notification-state {{ $comment->is_approved ? 'is-approved' : 'is-pending' }}">
                            {{ $comment->is_approved ? '✓ Опубликован' : 'На модерации' }}
                        </span>
                        <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->format('d.m.Y, H:i') }}</time>
                    </div>
                    <h3>{{ $comment->article?->title ?? 'Статья удалена' }}</h3>
                    <p>{{ $comment->body }}</p>
                    @if ($comment->is_approved && $comment->article && ($comment->article->is_published || auth()->user()->isModerator()))
                        <a href="{{ route('news.show', $comment->article) }}#comment-{{ $comment->id }}">Посмотреть под статьёй ↗</a>
                    @endif
                </article>
            @empty
                <div class="profile-notification-empty">У вас ещё нет комментариев. Откройте любую опубликованную новость и присоединитесь к обсуждению.</div>
            @endforelse
        </div>
        {{ $comments->links('vendor.pagination.pink') }}
    </section>

    <div class="profile-notifications-head">
        <div>
            <h2>Мои <span class="serif-accent">уведомления.</span></h2>
            <p>Когда модератор примет или отклонит ваш комментарий, здесь появится результат.</p>
        </div>
        @if (auth()->user()->unreadNotifications()->exists())
            <form method="post" action="{{ route('account.notifications.read') }}">
                @csrf
                <button type="submit" class="button button-secondary">Отметить как прочитанные</button>
            </form>
        @endif
    </div>
    @if (session('status'))<div class="admin-notice" role="status">{{ session('status') }}</div>@endif
    <div class="profile-notifications-list">
        @forelse ($notifications as $notification)
            <article class="profile-notification {{ $notification->read_at === null ? 'is-unread' : '' }}">
                <div class="profile-notification-top">
                    <span class="notification-state {{ ($notification->data['status'] ?? '') === 'approved' ? 'is-approved' : 'is-rejected' }}">
                        {{ ($notification->data['status'] ?? '') === 'approved' ? '✓ Комментарий опубликован' : 'Комментарий отклонён' }}
                    </span>
                    <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->format('d.m.Y, H:i') }}</time>
                </div>
                <h3>{{ $notification->data['article_title'] ?? 'Статья' }}</h3>
                <p>{{ $notification->data['comment_excerpt'] ?? '' }}</p>
                @if (($notification->data['status'] ?? '') === 'approved')
                    <a href="{{ route('news.show', ['article' => $notification->data['article_id']]) }}#comments">Посмотреть комментарий ↗</a>
                @endif
            </article>
        @empty
            <div class="profile-notification-empty">Пока нет уведомлений. После проверки ваших комментариев они появятся здесь.</div>
        @endforelse
    </div>
    {{ $notifications->links('vendor.pagination.pink') }}
</section>
@endsection
