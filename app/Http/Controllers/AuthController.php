<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Validator;

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
      $token = $request->query->get("token");
      $user = User::showUserByToken($token, ["id"]);
      if ($user && $token != null) {
        User::loginUsingId($user->id);
        Auth::logout();
        return response()->json([
          "status" => true
        ]);
      }
      else {
        return response()->json([
          "status" => false,
          "errCode" => "tokenNotFound",
          "errMsg" => "This token is not associated with any user"
        ]);
      }
    }

    public function register(Request $request) {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
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
