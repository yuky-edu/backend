<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
  protected $fillable = [
    	'yclass_session',	'name',	'avatar',	'token', 'score'
  ];

  public function yclass_session()
  {
    return $this->belongsTo('App\YclassSession', 'yclass_session', 'id');
  }

  public function answer()
  {
    return $this->hasMany('App\PlayerAnswer', 'player', 'id');
  }

  static function register($yclass_session_id, $name, $avatar)
  {
    return Player::create([
      'yclass_session' => $yclass_session_id,
      'name' => $name,
      'avatar' => $avatar,
      'token' => md5($yclass_session_id.date('dmyhis'))
    ]);
  }

  static function removeById($user, $id)
  {
    $data = Player::with('yclass_session:id,yclass', 'yclass_session.yclass:id,user')->whereHas('yclass_session.yclass', function($q) use($user) {
      $q->where('user', '=', $user);
    })->where([
      ["id", "=", $id]
    ])->select('id', 'yclass_session')->first();
    if (!$data) return false;
    return $data->delete();
  }

  static function updatePlayer($user, $id, $key = [])
  {
    $data = Player::where([
      ["id", "=", $id]
    ])->select('id', 'name', 'avatar', 'score')->first();
    if (!$data) return false;
    $score = $data->score + $key["score"];
    $data->score = $score;
    return $data->save();
  }
}
