<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
      $check = User::login($request->email, $request->password);
      $result = [
        "status" => $check
      ];
      if ($check) {
        $userToken = User::rememberToken(["email", "=", $request->email]);
        $result["token"] = $userToken;
      }
      return response()->json($result);
    }

    public function logout(Request $request) {
      $token = $request->bearerToken();
      $id = User::showUserByToken($token, ["id"])->id;
      User::loginUsingId(1);
      Auth::logout();
      return response()->json([
        "status" => true
      ]);
    }

    public function register(Request $request) {
      $stored = User::store(
        $request->name,
        $request->email,
        $request->password
      );
      $status = $stored ? true : false;
      return response()->json([
        "status" => $status
      ]);
    }
}
