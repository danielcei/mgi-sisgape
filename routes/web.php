<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('/login', function () {
    return redirect(route('filament.mps.auth.login'));
})->name('login');




Route::get('/image/{path}', function ($path) {
    try {
        $path = decrypt($path);
        $disk = Storage::disk('ftp');
        if ($disk->exists($path)) {

            $fileContents = Storage::disk('ftp')->get($path);
            Storage::disk('public')->put($path, $fileContents);
            return Storage::disk('public')->url($path);

            $fileContents = $disk->get($path);
            $mimeType = $disk->mimeType($path) ?? 'image/webp'; // Fornecer fallback para webp se necessário
            return response($fileContents, 200)->header('Content-Type', '');
        }
    } catch (\Exception $e) {
        return abort(404, "File not found.");
    }
    return abort(404, "File not found.");
});


Route::get('/', function () {
    return redirect('/admin');
});


