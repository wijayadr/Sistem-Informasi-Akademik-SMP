<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(route('admin.login'));
});

Route::prefix('admin-panel')->group(function () {
    Route::get('/login', App\Livewire\AdminPanel\Auth\Login::class)->name('admin.login');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', \App\Livewire\AdminPanel\Dashboard\Index::class)->name('admin.dashboard');

        // Master Data
        Route::get('/academic-years', App\Livewire\AdminPanel\AcademicYears\Index::class)->name('admin.academic-years.index');
        Route::get('/subjects', App\Livewire\AdminPanel\Subjects\Index::class)->name('admin.subjects.index');

        // Students
        Route::prefix('students')->name('admin.students.')->group(function () {
            Route::get('', App\Livewire\AdminPanel\students\Index::class)->name('index');
            Route::get('/create', \App\Livewire\AdminPanel\students\Form::class)->name('create');
            Route::get('/edit/{student}', \App\Livewire\AdminPanel\students\Form::class)->name('edit');
            Route::get('/{student}/parents', App\Livewire\AdminPanel\Students\ParentManagement::class)->name('parents');
        });

        // Teachers
        Route::prefix('teachers')->name('admin.teachers.')->group(function () {
            Route::get('', App\Livewire\AdminPanel\Teachers\Index::class)->name('index');
            Route::get('/create', \App\Livewire\AdminPanel\Teachers\Form::class)->name('create');
            Route::get('/edit/{teacher}', \App\Livewire\AdminPanel\Teachers\Form::class)->name('edit');
        });

        // Roles
        Route::get('/roles', \App\Livewire\AdminPanel\Roles\Index::class)->name('admin.roles.index');

        // Users
        Route::get('/users', App\Livewire\AdminPanel\Users\Index::class)->name('admin.users.index');
        Route::get('/users/create', \App\Livewire\AdminPanel\Users\Form::class)->name('admin.users.create');
        Route::get('/users/edit/{user}', \App\Livewire\AdminPanel\Users\Form::class)->name('admin.users.edit');

        // News
        Route::get('/news', \App\Livewire\AdminPanel\News\Index::class)->name('admin.news.index');
        Route::get('/news/create', \App\Livewire\AdminPanel\News\Form::class)->name('admin.news.create');
        Route::get('/news/edit/{news}', \App\Livewire\AdminPanel\News\Form::class)->name('admin.news.edit');

        // Categories
        Route::get('/categories', \App\Livewire\AdminPanel\Categories\Index::class)->name('admin.categories.index');

        Route::post('/logout', \App\Http\Controllers\Auth\LogoutController::class)->name('logout');
    });
});
