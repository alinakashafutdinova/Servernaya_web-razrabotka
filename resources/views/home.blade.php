@extends('layouts.app')

@section('title', 'Главная')

@section('content')
<section class="hero container">
    <div class="hero-content">
        <span class="eyebrow"><span class="eyebrow-dot"></span> МОЯ ВЕБ-ЛАБОРАТОРИЯ</span>
        <h1>Привет, я Алина<span class="pink-period">.</span><br><span class="hero-light">Рада знакомству!</span></h1>
        <p class="hero-description">Это мой небольшой уголок в интернете. Здесь я учусь веб-разработке, пробую новые идеи и постепенно превращаю их в работающие страницы.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('about') }}">Узнать обо мне <span aria-hidden="true">↗</span></a>
            <a class="text-link" href="{{ route('contacts') }}">Мои контакты <span aria-hidden="true">→</span></a>
        </div>
        <div class="hero-micro"><span class="micro-line"></span> Студентка · Веб-технологии · Москва</div>
    </div>
    <div class="hero-visual" aria-hidden="true">
        <div class="visual-grid"></div>
        <div class="poster-card">
            <div class="poster-top"><span>WEB / 001</span><span class="poster-sparkle">✳</span></div>
            <div class="poster-illustration"><span class="abstract-ring"></span><span class="abstract-shape"></span><span class="abstract-small"></span></div>
            <div class="poster-bottom"><span>learning by<br>creating.</span><span class="poster-arrow">↗</span></div>
        </div>
        <span class="visual-orbit orbit-one">✳</span>
        <span class="visual-orbit orbit-two">✴</span>
        <div class="visual-sticker">made with<br><b>curiosity ♡</b></div>
    </div>
</section>

<section class="highlights">
    <div class="container highlights-inner">
        <div class="section-heading"><span class="section-number">01 — 03</span><h2>С чего всё начинается</h2><p>Три вещи, которые для меня важны в разработке.</p></div>
        <div class="features">
            <article class="feature-card"><span class="feature-icon">✦</span><span class="feature-no">01</span><h3>Идея</h3><p>У каждого проекта должна быть понятная цель и своя маленькая история.</p></article>
            <article class="feature-card"><span class="feature-icon">◌</span><span class="feature-no">02</span><h3>Детали</h3><p>Люблю, когда сайт не только работает, но и выглядит аккуратно.</p></article>
            <article class="feature-card"><span class="feature-icon">↗</span><span class="feature-no">03</span><h3>Развитие</h3><p>С каждой новой работой осваиваю ещё что-то полезное.</p></article>
        </div>
    </div>
</section>
@endsection
