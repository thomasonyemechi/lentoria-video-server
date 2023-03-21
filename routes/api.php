<?php

use App\Http\Controllers\EbookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/video', [VideoController::class, 'uploadNewLectureVideo']);
Route::post('/book', [EbookController::class, 'uploadEbook']);
Route::get('/book/downloads/{course_id}', [EbookController::class, 'fetchEbookDownloadHistory']);


Route::get('/book/{file_name}/{user_id}', [EbookController::class, 'fetchEbook']);
