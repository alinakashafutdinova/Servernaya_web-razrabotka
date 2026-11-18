@extends('layouts.app')

@section('title', $article->title)

@section('content')
<section class="news-story container">
    <a class="news-back" href="{{ route('news.index') }}"><span aria-hidden="true">←</span> Ко всем новостям</a>
    <div class="news-story-content">
        <span class="eyebrow"><span class="eyebrow-dot"></span> СТАТЬЯ ИЗ БАЗЫ ДАННЫХ</span>
        <h1>{{ $article->title }}</h1>
        <time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->format('d.m.Y') }}</time>
        <p class="news-story-lead">{{ $article->summary }}</p>
        <div class="news-story-body">{{ $article->content }}</div>
    </div>
    <a href="{{ route('news.index') }}" class="button button-primary">Все новости <span aria-hidden="true">↗</span></a>
</section>
@endsection
