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
  // Player
  Route::post('/player/join', 'PlayerController@joinClass');
  Route::post('/player/register', 'PlayerController@register');

  // Yclass
  Route::get('/yclass/code/{code}', 'YclassController@getInfoClassByCode');

  Route::group([
    "middleware" => "PlayerMiddleware"
  ], function() {
    // Player
    Route::get('/player/myInfo', 'PlayerController@myInfo');
    Route::get('/player/countMyFriend', 'PlayerController@countMyFriend');

    // Yclass Session
    Route::get('/yclass_session/mysession', 'YclassSessionController@playSingleSession');

    // Player Answer
    Route::post('/player_answer', 'PlayerAnswerController@store');
    Route::delete('/player_answer/{id}', 'PlayerAnswerController@destroyAnswer');
    Route::get('/player_answer/entity/{id}', 'PlayerAnswerController@getByPlayerAndEntity');
    Route::get('/player_answer/session/{id_session}', 'PlayerAnswerController@getMyAnswerBySession');

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
  Route::get('/yclass/myclass/single', 'YclassController@getMyClassSingle');
  Route::put('/yclass/myclass/{id}', 'YclassController@updateMyClassSingle');
  Route::get('/yclass/myclass', 'YclassController@myclass');
  Route::get('/yclass/generateCode', 'YclassController@generateCode');
  Route::delete('/yclass/delete/{id}', 'YclassController@destroy');

  // Yclass Session
  Route::post('/yclass_session', 'YclassSessionController@store');
  Route::get('/yclass_session/single', 'YclassSessionController@getSingle');
  Route::put('/yclass_session/{id}', 'YclassSessionController@updateSession');
  Route::put('/yclass_session/{id}/entity', 'YclassSessionController@updateIndexEntity');
  Route::put('/yclass_session/{id}/answeredEntity', 'YclassSessionController@updateAnsweredEntity');

  // Entity
  Route::post('/entity/question', 'EntityController@store_question');
  Route::post('/entity/theory', 'EntityController@store_theory');
  Route::delete('/entity/{id}', 'EntityController@destroyMyE');
  Route::get('/entity/myentity/{id}', 'EntityController@getSingleMyEntity');
  Route::get('/entity/myentity/yclass/by', 'EntityController@getEntityBy');
  Route::get('/entity/question/myquestion/count/all', 'EntityController@countMyQuestion');
  Route::get('/entity/theory/mytheory/count/all', 'EntityController@countMyTheory');
  Route::post('/entity/theory/mytheory/{id}/update', 'EntityController@update_theory');
  Route::post('/entity/question/myquestion/{id}/update', 'EntityController@update_question');

  // User
  Route::get('/user/myInfo', 'UserController@myInfo');
  Route::put('/user/updateInfo', 'UserController@updateInfo');
  Route::put('/user/updatePassword', 'UserController@updatePassword');

  // Player
  Route::delete('/player/{id}', 'PlayerController@kick');
  Route::get('/player/session/{id_session}', 'PlayerController@getPlayersBySession');
  Route::put('/player/addScore/{id}', 'PlayerController@addScore');
  Route::get('/player/session/{id}/leaderboard', 'PlayerController@getLeaderboardByIdSession');

  // PlayerAnswer
  Route::get('/player_answer/entity/{id_entity}', 'PlayerAnswerController@getByIdEntity');
  Route::get('/player_answer/entity/{id_entity}/session/{id_session}/player', 'PlayerAnswerController@getIdPlayerWhoAnsweredByIdEntity');

});
