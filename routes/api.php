<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/test', function() {
  return response()->json([
    "status" => true,
    "msg" => "Hello World"
  ]);
});

Route::post('/test', function(Request $request) {
  $token = $request->bearerToken();
  return response()->json([
    "status" => true,
    "token" => $token,
    "bodyt" => $request->all()
  ]);
});

Route::group([
  "prefix" => "plays"
], function () {
  Route::get('/', function(){
    return "plays";
  });
});

Route::group([
  "prefix" => "auth"
], function () {
  Route::post('/login', "AuthController@login");
  Route::get('/logout', "AuthController@logout"); // HostMiddleware
  Route::post('/register', "AuthController@register");
});

Route::group([
  "prefix" => "hosts",
  "middleware" => "HostMiddleware"
], function () {
  // YclassCategory
  Route::get('/yclass_categories', 'YclassCategoryController@index');

  // YclassController
  Route::post('/yclass', 'YclassController@store');
  Route::get('/yclass/getById/{id}', 'YclassController@getById');
  Route::get('/yclass/myclass', 'YclassController@myclass');
  Route::delete('/yclass/delete/{id}', 'YclassController@destroy');
});
