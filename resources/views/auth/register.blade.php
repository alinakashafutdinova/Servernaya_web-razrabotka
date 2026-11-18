@extends('layouts.app')

@section('title', 'Создать аккаунт')

@section('content')
<section class="signup-page container account-page">
    <div class="signup-intro">
        <span class="eyebrow"><span class="eyebrow-dot"></span> ЛАБОРАТОРНАЯ РАБОТА №6</span>
        <h1>Привет, <span class="serif-accent">давай знакомиться.</span></h1>
        <p>Создай аккаунт, чтобы получить доступ к управлению новостями. Данные сохраняются в базе, а пароль хранится в виде защищённого хеша.</p>
        <div class="signup-sticker" aria-hidden="true">
            <span class="signup-sticker-star">✳</span>
            <span>join<br><em>the</em><br>club.</span>
            <span class="signup-sticker-foot">AUTH · SANCTUM · BLADE</span>
        </div>
        <div class="signup-step"><span>01</span> Заполни форму <span aria-hidden="true">↗</span></div>
        <div class="signup-step"><span>02</span> Подтверди пароль <span aria-hidden="true">↗</span></div>
        <div class="signup-step"><span>03</span> Перейди ко входу <span aria-hidden="true">↗</span></div>
    </div>
    <div class="signup-card">
        <div class="signup-card-header"><span class="signup-card-tag">НОВЫЙ АККАУНТ</span><span class="signup-card-icon" aria-hidden="true">✳</span></div>
        <h2>Создать <span>профиль</span></h2>
        <p class="signup-card-lead">Регистрация займёт всего пару минут.</p>
        <form method="post" action="{{ route('register.store') }}" class="signup-form">
            @csrf
            <div class="signup-field">
                <label for="register-name">Имя <span>*</span></label>
                <input id="register-name" type="text" name="name" value="{{ old('name') }}" maxlength="80" autocomplete="name" placeholder="Как тебя зовут?" required @error('name') aria-invalid="true" @enderror>
                @error('name')<p class="signup-error">{{ $message }}</p>@enderror
            </div>
            <div class="signup-field">
                <label for="register-email">Электронная почта <span>*</span></label>
                <input id="register-email" type="email" name="email" value="{{ old('email') }}" maxlength="254" autocomplete="email" placeholder="example@mail.ru" required @error('email') aria-invalid="true" @enderror>
                @error('email')<p class="signup-error">{{ $message }}</p>@enderror
            </div>
            <div class="signup-field">
                <label for="register-password">Пароль <span>*</span></label>
                <input id="register-password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="От 8 символов" required @error('password') aria-invalid="true" @enderror>
                @error('password')<p class="signup-error">{{ $message }}</p>@enderror
            </div>
            <div class="signup-field">
                <label for="register-password-confirmation">Повтори пароль <span>*</span></label>
                <input id="register-password-confirmation" type="password" name="password_confirmation" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Повтори пароль" required>
            </div>
            <button class="button button-primary signup-submit" type="submit">Зарегистрироваться <span aria-hidden="true">↗</span></button>
        </form>
        <div class="signup-card-note">Уже есть аккаунт? <a class="auth-inline-link" href="{{ route('login') }}">Войти</a></div>
    </div>
</section>
@endsection
