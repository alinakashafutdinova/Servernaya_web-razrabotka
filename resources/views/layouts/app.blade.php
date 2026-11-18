<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fffafc">
    <meta name="description" content="Учебный сайт по серверной веб-разработке: главная, новости, обо мне и контакты.">
    <title>@yield('title') — Алина Кашафутдинова</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
    <div class="site-shell">
        <header class="site-header">
            <div class="container header-inner">
                <a class="brand" href="{{ route('home') }}" aria-label="На главную страницу">
                    <span class="brand-symbol" aria-hidden="true">a<span>.</span></span>
                    <span class="brand-caption">alina <span>•</span> web lab</span>
                </a>
                <nav class="navigation" aria-label="Основное меню">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home', 'galery') ? 'active' : '' }}" {{ request()->routeIs('home') ? 'aria-current=page' : '' }}>Главная</a>
                    <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" {{ request()->routeIs('about') ? 'aria-current=page' : '' }}>О нас</a>
                    <a href="{{ route('contacts') }}" class="nav-link {{ request()->routeIs('contacts') ? 'active' : '' }}" {{ request()->routeIs('contacts') ? 'aria-current=page' : '' }}>Контакты</a>
                    <a href="{{ route('news.index') }}" class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" {{ request()->routeIs('news.*') ? 'aria-current=page' : '' }}>Новости</a>
                    @auth
                        @can('create', App\Models\Article::class)
                        <a href="{{ route('news.create') }}" class="nav-link {{ request()->routeIs('news.create') ? 'active' : '' }}">Создать новость</a>
                        <a href="{{ route('news.manage') }}" class="nav-link {{ request()->routeIs('news.manage', 'news.create', 'news.edit') ? 'active' : '' }}" {{ request()->routeIs('news.manage', 'news.create', 'news.edit') ? 'aria-current=page' : '' }}>Управление</a>
                        @endcan
                        @can('manage-comments')
                            <a href="{{ route('comments.moderation') }}" class="nav-link {{ request()->routeIs('comments.moderation') ? 'active' : '' }}" {{ request()->routeIs('comments.moderation') ? 'aria-current=page' : '' }}>Модерация</a>
                        @endcan
                        <form method="post" action="{{ route('logout') }}" class="nav-logout-form">
                            @csrf
                            <button type="submit" class="nav-link nav-logout">Выйти</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login', 'login.store') ? 'active' : '' }}">Войти</a>
                        <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register', 'register.store') ? 'active' : '' }}">Регистрация</a>
                    @endauth
                </nav>
                <span class="header-note">09 / 09</span>
            </div>
        </header>

        <main id="main-content" class="site-main">
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="container footer-inner">
                <div class="footer-details">
                    <span class="footer-mark">a.</span>
                    <span>Кашафутдинова Алина Тимуровна</span>
                    <span class="footer-separator">/</span>
                    <span>Группа 257-321</span>
                </div>
                <span class="footer-course">Серверная веб-разработка · Лабораторная №9</span>
            </div>
        </footer>
    </div>
</body>
</html>
