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
    "body" => $request->all()
  ]);
});

Route::group([
  "prefix" => "debug"
], function () {
  Route::get('/deleteAllUser', "DebugController@deleteAllUser");
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
  Route::get('/checkToken', "AuthController@checkToken");
});

Route::group([
  "prefix" => "hosts",
  "middleware" => "HostMiddleware"
], function () {
  // YclassCategory
  Route::get('/yclass_categories', 'YclassCategoryController@index');

  // Yclass
  Route::post('/yclass', 'YclassController@store');
  Route::get('/yclass/myclass/{id}', 'YclassController@getMyClassSingle');
  Route::put('/yclass/myclass/{id}', 'YclassController@updateMyClassSingle');
  Route::get('/yclass/myclass', 'YclassController@myclass');
  Route::get('/yclass/generateCode', 'YclassController@generateCode');
  Route::delete('/yclass/delete/{id}', 'YclassController@destroy');

  // Yclass Session
  Route::post('/yclass_session', 'YclassSessionController@store');
  Route::get('/yclass_session/single', 'YclassSessionController@getSingle');
  Route::put('/yclass_session/{id}', 'YclassSessionController@updateSession');
  Route::put('/yclass_session/{id}/question', 'YclassSessionController@updateIndexQuestion');

  // Question
  Route::post('/question', 'QuestionController@store');
  Route::delete('/question/myquestion/{id}', 'QuestionController@destroyMyQ');
  Route::get('/question/myquestion/{id}', 'QuestionController@getQuestionsById');
  Route::post('/question/myquestion/{id}/update', 'QuestionController@update');
  Route::get('/question/myquestion/yclass/{id_yclass}', 'QuestionController@getQuestionsByIdYClass');

  // User
  Route::get('/user/myInfo', 'UserController@myInfo');
  Route::put('/user/updateInfo', 'UserController@updateInfo');
  Route::put('/user/updatePassword', 'UserController@updatePassword');
});
