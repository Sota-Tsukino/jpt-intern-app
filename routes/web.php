<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\HomeController as StudentHomeController;
use App\Http\Controllers\Student\EntryController as StudentEntryController;
use App\Http\Controllers\Teacher\HomeController as TeacherHomeController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
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
    return view('welcome');
});

// 生徒用ルート
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/home', [StudentHomeController::class, 'index'])->name('home');
    Route::get('/entries/{entry}', [StudentEntryController::class, 'show'])->name('entries.show');
    Route::get('/entries/{entry}/edit', [StudentEntryController::class, 'edit'])->name('entries.edit');
    Route::patch('/entries/{entry}', [StudentEntryController::class, 'update'])->name('entries.update');
    // TODO: 以下は後で実装
    // Route::get('/entries/create', [StudentEntryController::class, 'create'])->name('entries.create');
});

// 担任用ルート
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/home', [TeacherHomeController::class, 'index'])->name('home');
});

// 管理者用ルート
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', [AdminHomeController::class, 'index'])->name('home');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
