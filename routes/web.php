<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\EquipoController;

Route::get('/dashboard', [EquipoController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/equipos', [EquipoController::class, 'store'])->middleware(['auth', 'verified'])->name('equipos.store');
Route::post('/equipos/{equipo}/reassign', [EquipoController::class, 'reassign'])->middleware(['auth', 'verified'])->name('equipos.reassign');
Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])->middleware(['auth', 'verified'])->name('equipos.destroy');
Route::post('/equipos/{equipo}/estado', [EquipoController::class, 'updateEstado'])->middleware(['auth', 'verified'])->name('equipos.updateEstado');
Route::post('/equipos/{equipo}/comentarios', [EquipoController::class, 'storeComentario'])->middleware(['auth', 'verified'])->name('equipos.comentarios.store');
Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->middleware(['auth'])->name('admin.index');
Route::get('/admin/usuarios', [\App\Http\Controllers\Admin\UserController::class, 'index'])->middleware(['auth'])->name('admin.users.index');
Route::post('/admin/usuarios/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'setRole'])->middleware(['auth'])->name('admin.users.setRole');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
