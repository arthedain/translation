<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/endpoint', function (Request $request) {
//     //
// });

Route::get('/get', 'Arthedain\Translation\Http\Controllers\TranslationController@get');
Route::post('/update', 'Arthedain\Translation\Http\Controllers\TranslationController@update');
Route::post('/delete', 'Arthedain\Translation\Http\Controllers\TranslationController@delete');
Route::get('/getByTab/{tab}', 'Arthedain\Translation\Http\Controllers\TranslationController@getByTab');
