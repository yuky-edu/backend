<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
  protected $fillable = [
    	'yclass_session',	'name',	'photo',	'token'
  ];

  public function yclass_session()
  {
    return $this->belongsTo('App\YclassSession', 'yclass_session', 'id');
  }

  static function register($yclass_session_id, $name, $photo)
  {
    return Player::create([
      'yclass_session' => $yclass_session_id,
      'name' => $name,
      'photo' => $photo,
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

  static function updatePlayer($id, $key = [])
  {
    $data = Player::where([
      ["id", "=", $id]
    ])->select('id', 'name', 'photo')->first();
    if (!$data) return false;
    if (isset($key['name'])) $data->name = $key['name'];
    if (isset($key['photoName'])) $data->photo = $key['photoName'];
    return $data->save();
  }
}
