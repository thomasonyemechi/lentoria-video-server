<?php

use App\Http\Controllers\EbookController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great! 
|
*/

Route::get('/', function () {
    return abort(404);
});


Route::get('/stream', function () {
    return view('stream');
});


Route::get('/hash/{length}', [VideoController::class, 'win_hashs']);
Route::get('/watchvideo/{hash}', [VideoController::class, 'fetchVideo']);
Route::post('/video', [VideoController::class, 'uploadNewLectureVideo']);


Route::get('/book/{file_name}/{user_id}', [EbookController::class, 'fetchEbook']);

