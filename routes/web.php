<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('/about', 'about')->name('about');

Route::get('/contacts', function () {
    $contacts = [
        [
            'title' => 'GitHub',
            'value' => '@alinakashafutdinova',
            'description' => 'Мой профиль и другие проекты',
            'url' => 'https://github.com/alinakashafutdinova',
            'icon' => '01',
        ],
        [
            'title' => 'Репозиторий',
            'value' => 'Серверная веб-разработка',
            'description' => 'Исходный код лабораторной работы',
            'url' => 'https://github.com/alinakashafutdinova/Servernaya_web-razrabotka',
            'icon' => '02',
        ],
    ];

    return view('contacts', ['contacts' => $contacts]);
})->name('contacts');
