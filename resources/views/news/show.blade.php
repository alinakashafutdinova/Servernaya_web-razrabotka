@extends('layouts.app')

@section('title', $article->title)

@section('content')
<section class="news-story container">
    <a class="news-back" href="{{ route('news.index') }}"><span aria-hidden="true">←</span> Ко всем новостям</a>

    <div class="news-story-grid">
        <article class="news-story-content">
            <span class="eyebrow"><span class="eyebrow-dot"></span> СТАТЬЯ ИЗ БАЗЫ ДАННЫХ</span>
            <h1>{{ $article->title }}</h1>
            <time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->format('d.m.Y') }}</time>
            <p class="news-story-lead">{{ $article->summary }}</p>
            <div class="news-story-body">{{ $article->content }}</div>
            <a href="{{ route('news.index') }}" class="button button-primary news-story-all">Все новости <span aria-hidden="true">↗</span></a>
        </article>

        <aside class="news-story-aside" aria-label="Дополнительно о статьях">
            <div class="news-story-visual">
                <img src="{{ asset('images/news-illustration.svg') }}" width="760" height="660" alt="Иллюстрация веб-страницы с розовыми акцентами" loading="lazy">
                <div class="news-visual-caption">
                    <span class="news-visual-label">МОИ ЗАМЕТКИ</span>
                    <strong>О веб-разработке и не только.</strong>
                    <p>Собираю знания в одном месте.</p>
                </div>
            </div>

            @if ($relatedArticles->isNotEmpty())
                <div class="news-related">
                    <h2>Ещё почитать <span aria-hidden="true">↗</span></h2>
                    <ul>
                        @foreach ($relatedArticles as $relatedArticle)
                            <li>
                                <a href="{{ route('news.show', $relatedArticle) }}">
                                    <span class="news-related-title">{{ $relatedArticle->title }}</span>
                                    <time datetime="{{ $relatedArticle->published_at->format('Y-m-d') }}">{{ $relatedArticle->published_at->format('d.m.Y') }}</time>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
</section>
@endsection
