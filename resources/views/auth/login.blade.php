@extends('layouts.app')

@section('title', 'Вход')

@section('content')
<section class="signup-page container account-page">
    <div class="signup-intro">
        <span class="eyebrow"><span class="eyebrow-dot"></span> ЛАБОРАТОРНАЯ РАБОТА №6</span>
        <h1>Снова <span class="serif-accent">привет!</span></h1>
        <p>Войди в аккаунт, чтобы создавать, изменять и удалять статьи. Без авторизации управление закрыто.</p>
        <div class="signup-sticker" aria-hidden="true">
            <span class="signup-sticker-star">✳</span>
            <span>welcome<br><em>back.</em></span>
            <span class="signup-sticker-foot">LOGIN · SESSION · SANCTUM</span>
        </div>
    </div>
    <div class="signup-card">
        <div class="signup-card-header"><span class="signup-card-tag">ВХОД В ПРОФИЛЬ</span><span class="signup-card-icon" aria-hidden="true">✳</span></div>
        <h2>С возвращением<span>!</span></h2>
        <p class="signup-card-lead">Введи свои данные и продолжим.</p>
        @if (session('status'))<div class="auth-notice" role="status">{{ session('status') }}</div>@endif
        <form method="post" action="{{ route('login.store') }}" class="signup-form">
            @csrf
            <div class="signup-field">
                <label for="login-email">Электронная почта <span>*</span></label>
                <input id="login-email" type="email" name="email" value="{{ old('email') }}" maxlength="254" autocomplete="email" placeholder="example@mail.ru" required @error('email') aria-invalid="true" @enderror>
                @error('email')<p class="signup-error">{{ $message }}</p>@enderror
            </div>
            <div class="signup-field">
                <label for="login-password">Пароль <span>*</span></label>
                <input id="login-password" type="password" name="password" autocomplete="current-password" placeholder="Твой пароль" required @error('password') aria-invalid="true" @enderror>
                @error('password')<p class="signup-error">{{ $message }}</p>@enderror
            </div>
            <button class="button button-primary signup-submit" type="submit">Войти <span aria-hidden="true">↗</span></button>
        </form>
        <div class="signup-card-note">Нет аккаунта? <a class="auth-inline-link" href="{{ route('register') }}">Зарегистрироваться</a></div>
    </div>
</section>
@endsection
