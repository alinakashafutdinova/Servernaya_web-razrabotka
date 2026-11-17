<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/galery/{id}', [MainController::class, 'gallery'])->whereNumber('id')->name('galery');

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

Route::get('/signin', [AuthController::class, 'create'])->name('signin.create');
Route::post('/signin', [AuthController::class, 'registration'])->name('signin.registration');
