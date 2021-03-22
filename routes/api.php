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
  "prefix" => "hosts"
], function () {
  Route::get('/', function(){
    return "host";
  });
});
