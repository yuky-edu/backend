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
        'name', 'email', 'password',
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

    static function store($name, $email, $password){
      return User::create([
        'name' => $name,
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

    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at'
    ];
}
