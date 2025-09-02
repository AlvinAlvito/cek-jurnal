<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\JurnalcekController;
use App\Http\Controllers\KontrolJurnalController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DashboardController;
// ===================
// Halaman Login
// ===================
Route::get('/', [PublicController::class, 'index'])->name('login');        // tampilkan form
Route::post('/cek-ketersediaan', [PublicController::class, 'cek'])->name('public.cek');  // proses cek

// ===================
// Proses Login Manual
// ===================
Route::post('/', function (Request $request) {
    $username = $request->username;
    $password = $request->password;

    if ($username === 'gibran' && $password === '19juta') {
        session(['is_admin' => true]);
        return redirect('/admin');
    }

    return back()->withErrors(['login' => 'Username atau Password salah!']);
})->name('login.proses');

// ===================
// Logout
// ===================
Route::get('/logout', function () {
    session()->forget('is_admin');
    return redirect('/');
})->name('logout');


// Dashboard admin

Route::get('/admin', function () {
    if (!session('is_admin')) return redirect('/');
    return app(DashboardController::class)->index();
})->name('index');



// ===================
// CRUD Data Dosen
// ===================
Route::get('/admin/data-dosen', function () {
    if (!session('is_admin')) return redirect('/');
    return app(DosenController::class)->index();
})->name('dosen.index');

Route::post('/admin/data-dosen', function (Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(DosenController::class)->store($request);
})->name('dosen.store');

Route::delete('/admin/data-dosen/{id}', function ($id) {
    if (!session('is_admin')) return redirect('/');
    return app(DosenController::class)->destroy($id);
})->name('dosen.destroy');
Route::put('/admin/data-dosen/{id}', function (Request $request, $id) {
    if (!session('is_admin')) return redirect('/');
    return app(App\Http\Controllers\DosenController::class)->update($request, $id);
})->name('dosen.update');

// ===================
// CRUD Data Mahasiswa
// ===================
Route::get('/admin/data-mahasiswa', function () {
    if (!session('is_admin')) return redirect('/');
    return app(MahasiswaController::class)->index();
})->name('mahasiswa.index');

Route::post('/admin/data-mahasiswa', function (Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(MahasiswaController::class)->store($request);
})->name('mahasiswa.store');

Route::delete('/admin/data-mahasiswa/{id}', function ($id) {
    if (!session('is_admin')) return redirect('/');
    return app(MahasiswaController::class)->destroy($id);
})->name('mahasiswa.destroy');
Route::put('/admin/data-mahasiswa/{id}', function (Request $request, $id) {
    if (!session('is_admin')) return redirect('/');
    return app(App\Http\Controllers\MahasiswaController::class)->update($request, $id);
})->name('mahasiswa.update');

// ===================
// CRUD Data jurnal
// ===================
Route::get('/admin/data-jurnal', function () {
    if (!session('is_admin')) return redirect('/');
    return app(JurnalController::class)->index();
})->name('jurnal.index');

Route::post('/admin/data-jurnal', function (Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(JurnalController::class)->store($request);
})->name('jurnal.store');

Route::delete('/admin/data-jurnal/{id}', function ($id) {
    if (!session('is_admin')) return redirect('/');
    return app(JurnalController::class)->destroy($id);
})->name('jurnal.destroy');
Route::put('/admin/data-jurnal/{id}', function (Request $request, $id) {
    if (!session('is_admin')) return redirect('/');
    return app(App\Http\Controllers\JurnalController::class)->update($request, $id);
})->name('jurnal.update');

// ===================
// CRUD Data Cek jurnal
// ===================
Route::get('/admin/data-jurnalcek', function () {
    if (!session('is_admin')) return redirect('/');
    return app(JurnalcekController::class)->index();
})->name('jurnalcek.index');

Route::post('/admin/data-jurnalcek', function (Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(JurnalcekController::class)->store($request);
})->name('jurnalcek.store');

Route::delete('/admin/data-jurnalcek/{id}', function ($id) {
    if (!session('is_admin')) return redirect('/');
    return app(JurnalcekController::class)->destroy($id);
})->name('jurnalcek.destroy');
Route::put('/admin/data-jurnalcek/{id}', function (Request $request, $id) {
    if (!session('is_admin')) return redirect('/');
    return app(App\Http\Controllers\JurnalcekController::class)->update($request, $id);
})->name('jurnalcek.update');


// Kontrol jurnal
Route::get('/admin/kontrol-jurnal', function () {
    if (!session('is_admin')) return redirect('/');
    return app(KontrolJurnalController::class)->index();
})->name('kontroljurnal.index');

Route::post('/admin/kontrol-jurnal/rules', function (Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(KontrolJurnalController::class)->updateRules($request);
})->name('kontroljurnal.rules');