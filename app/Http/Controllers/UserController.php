<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Hash;

class UserController extends Controller
{
  public function updateInfo(Request $request) {
    $validator = Validator::make($request->all(), [
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required|email'
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }
    $users_id = $request->get("myid");
    $updated = User::updateInfo($users_id, $request->all());
    $status = $updated ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function updatePassword(Request $request) {
    $validator = Validator::make($request->all(), [
      'old' => 'required',
      'new' => 'required'
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }
    $users_id = $request->get("myid");
    $old = $request->old;
    $new = $request->new;

    $password = User::where([
      ["id", "=", $users_id]
    ])->select("password")->first()->password;
    if (!Hash::check($old, $password)) {
      return response()->json([
        "status" => false,
        "errCode" => "wrongPass",
        "errMsg" => "wrong old password for this user"
      ]);
    }

    $updated = User::updatePassword($users_id, $new);
    $status = $updated ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function myInfo(Request $request) {
    $users_id = $request->get("myid");
    return User::myInfo($users_id);
  }
}
