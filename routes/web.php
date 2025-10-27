<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\HomeController as StudentHomeController;
use App\Http\Controllers\Student\EntryController as StudentEntryController;
use App\Http\Controllers\Teacher\HomeController as TeacherHomeController;
use App\Http\Controllers\Teacher\EntryController as TeacherEntryController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\ClassController as AdminClassController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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
    return redirect()->route('login');
});

// 生徒用ルート
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/home', [StudentHomeController::class, 'index'])->name('home');
    Route::get('/entries/create', [StudentEntryController::class, 'create'])->name('entries.create');
    Route::post('/entries', [StudentEntryController::class, 'store'])->name('entries.store');
    Route::get('/entries/{entry}', [StudentEntryController::class, 'show'])->name('entries.show');
    Route::get('/entries/{entry}/edit', [StudentEntryController::class, 'edit'])->name('entries.edit');
    Route::patch('/entries/{entry}', [StudentEntryController::class, 'update'])->name('entries.update');
});

// 担任用ルート
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/home', [TeacherHomeController::class, 'index'])->name('home');
    Route::get('/entries', [TeacherEntryController::class, 'index'])->name('entries.index');
    Route::get('/entries/{entry}', [TeacherEntryController::class, 'show'])->name('entries.show');

    // 課題2: スタンプ・フラグ機能
    Route::patch('/entries/{entry}/stamp', [TeacherEntryController::class, 'stamp'])->name('entries.stamp');
    Route::patch('/entries/{entry}/flag', [TeacherEntryController::class, 'updateFlag'])->name('entries.updateFlag');
});

// 管理者用ルート
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', [AdminHomeController::class, 'index'])->name('home');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.resetPassword');
    Route::get('/classes', [AdminClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [AdminClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [AdminClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}/edit', [AdminClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [AdminClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [AdminClassController::class, 'destroy'])->name('classes.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
