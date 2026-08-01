<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LoginController;
use App\Models\Barang;


use Illuminate\Support\Facades\Storage;

Route::get('/cek-storage', function () {

    Storage::disk('public')->put('tes.txt', 'Halo Railway');

    if (Storage::disk('public')->exists('tes.txt')) {
        return "BERHASIL";
    }

    return "GAGAL";
});
Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'index'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login.authenticate');

});


Route::middleware('auth')->group(function () {

    Route::get('/', function () {

        $totalproduk = Barang::count();

        $totaliphone = Barang::where('kategori', 'iPhone')->count();

        $totalandroid = Barang::where('kategori', 'Android')->count();

        $jumlahjenis = Barang::distinct('kategori')->count('kategori');

        return view('welcome', compact(
            'totalproduk',
            'totaliphone',
            'totalandroid',
            'jumlahjenis'
        ));

    })->name('dashboard');

    Route::resource('admin', BarangController::class);


    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});