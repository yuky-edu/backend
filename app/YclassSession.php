<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Entity;

class YclassSession extends Model
{
    protected $fillable = [
      'yclass',
      'index_entity',
      'ws_channel',
      'answered_entity',
      'status'
    ];

    public function yclass()
    {
      return $this->belongsTo('App\Yclass', 'yclass', 'id');
    }

    static function store($yclass)
    {
      return YclassSession::create([
        'yclass' => $yclass,
        'ws_channel' => md5(date("dmyhisu"))
      ]);
    }

    static function single($user, $where)
    {
      return YclassSession::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
        $q->where("user", "=", $user);
      })->where($where)->first();
    }

    static function updateSession($user, $id, $r)
    {
      $data = YclassSession::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
        $q->where("user", "=", $user);
      })->where([
        ["id", "=", $id]
      ])->first();
      if (!$data) return false;
      if(array_key_exists("status", $r)) $data->status = (string) $r["status"];
      return $data->save();
    }

    static function updateAnsweredEntity($user, $id, $answeredEntity)
    {
      $data = YclassSession::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
        $q->where("user", "=", $user);
      })->where([
        ["id", "=", $id]
      ])->first();
      if (!$data) return false;
      $AE = json_decode($data->answered_entity);
      array_push($AE, $answeredEntity);
      $data->answered_entity = json_encode($AE);
      return (object) [
        "status" => $data->save(),
        "answered_entity" => $AE
      ];
    }

    static function updateIndexEntity($user, $id, $q)
    {
      $data = YclassSession::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
        $q->where("user", "=", $user);
      })->where([
        ["id", "=", $id]
      ])->first();
      if (!$data) return false;
      $totalQ = Entity::where([
        ["yclass", "=", $data->yclass]
      ])->select('id')->count();
      $totalIndexQ = $totalQ-1;
      if (is_bool($q)) {
        $nextIndex = $data->index_entity+1;
      }
      else {
        $nextIndex = $q;
      }
      if ($totalIndexQ < $nextIndex) {
        return (object) [
          "status" => false,
          "errCode" => "end",
          "errMsg" => "theres no entity anymore in this yclass"
        ];
      }
      $data->index_entity = $nextIndex;
      return (object) [
        "status" => $data->save(),
        "index" => $nextIndex,
        "answered_entity" => $data->answered_entity
      ];
    }

    // Play
    static function playSingleSession($id)
    {
      return YclassSession::with('yclass:id,title,description,code')->where([
        ["id", "=", $id]
      ])->first();
    }

    static function deleteSession($user, $id)
    {
      $data = YclassSession::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
        $q->where("user", "=", $user);
      })->where([
        ["id", "=", $id]
      ])->first();
      return $data->delete();
    }
}
