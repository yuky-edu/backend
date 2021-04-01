<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    static function login($email, $password){
      return Auth::attempt([
        'email' => $email,
        'password' => $password
      ], true);
    }

    static function rememberToken($where){
      $data = User::select("remember_token")->where([
        $where
      ])->first();
      return $data->remember_token;
    }

    static function store($first_name, $last_name, $email, $password){
      return User::create([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'password' => bcrypt($password)
      ]);
    }

    static function showUserByToken($token, $select = []) {
      return User::select(...$select)->where([
        ["remember_token", "=", $token]
      ])->first();
    }

    static function loginUsingId($id) {
      return Auth::loginUsingId($id);
    }

    static function updateInfo($users_id, $info) {
      try {
        $data = User::where([
          ["id", "=", $users_id]
        ])->first();
        foreach ($info as $key => $value) {
          $data[$key] = $value;
        }
        return $data->save();
      } catch (\Exception $e) {
        return response()->json([
          "status" => false,
          "errCode" => "-",
          "errMsg" => $e->getMessage()
        ]);
      }
    }

    static function updatePassword($users_id, $pass) {
      try {
        $data = User::where([
          ["id", "=", $users_id]
        ])->select("password")->first();
        $data->password = bcrypt($pass);
        return $data->save();
      } catch (\Exception $e) {
        return response()->json([
          "status" => false,
          "errCode" => "-",
          "errMsg" => $e->getMessage()
        ]);
      }
    }

    static function myInfo($users_id) {
      return User::where([
        ["id", "=", $users_id]
      ])->first();
    }

    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'id'
    ];
}
