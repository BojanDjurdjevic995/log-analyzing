<?php

use Illuminate\Http\Request;

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
Route::middleware('api_token')->group(function (){
    Route::get('/', 'LogController@index')->name('log.index');
    Route::post('log', 'LogController@store')->name('log.store');
    Route::get('log/{name?}', 'LogController@download')->name('log.download');
    Route::delete('log/{name?}', 'LogController@destroy')->name('log.destroy');

    Route::prefix('aggregate')->group(function (){
        Route::get('ip', 'AggregateController@getAggregateByIp')->name('aggregate.ip');
        Route::get('method', 'AggregateController@getAggregateByMethod')->name('aggregate.method');
        Route::get('url', 'AggregateController@getAggregateByUrl')->name('aggregate.url');
    });
});
