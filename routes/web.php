<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\NewsController;
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

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/management', [NewsController::class, 'manage'])->name('news.manage');
Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
Route::post('/news', [NewsController::class, 'store'])->name('news.store');
Route::get('/news/{article}/edit', [NewsController::class, 'edit'])->whereNumber('article')->name('news.edit');
Route::put('/news/{article}', [NewsController::class, 'update'])->whereNumber('article')->name('news.update');
Route::delete('/news/{article}', [NewsController::class, 'destroy'])->whereNumber('article')->name('news.destroy');
Route::get('/news/{article}', [NewsController::class, 'show'])->whereNumber('article')->name('news.show');
