@extends('layouts.app')

@section('title', $project['title'])

@section('content')
<section class="gallery-page container">
    <nav class="breadcrumbs" aria-label="Навигационная цепочка">
        <a href="{{ route('home') }}">Главная</a><span aria-hidden="true">/</span><span>Галерея</span>
    </nav>
    <div class="gallery-heading">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> МОЯ ГАЛЕРЕЯ / {{ $project['category'] }}</span>
            <h1>{{ $project['title'] }}<span class="pink-period">.</span></h1>
            <p>{{ $project['description'] }}</p>
        </div>
        <a class="gallery-back" href="{{ route('home') }}#works"><span aria-hidden="true">←</span> Все заметки</a>
    </div>
    <figure class="gallery-figure">
        <img src="{{ asset($project['full_image']) }}" alt="Полная иллюстрация: {{ $project['title'] }}" width="1440" height="900">
        <figcaption>Полноразмерная иллюстрация · {{ $project['category'] }}</figcaption>
    </figure>
    <div class="gallery-footer-link"><a class="button button-primary" href="{{ route('home') }}#works">Вернуться к заметкам <span aria-hidden="true">↗</span></a></div>
</section>
@endsection
