<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Question;

class YclassSession extends Model
{
    protected $fillable = [
      'yclass',
      'index_question',
      'isExplain',
      'played'
    ];

    public function yclass()
    {
      return $this->belongsTo('App\Yclass', 'yclass', 'id');
    }

    static function store($yclass)
    {
      return YclassSession::create([
        'yclass' => $yclass
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
      if(array_key_exists("played", $r)) $data->played = (string) $r["played"];
      if(array_key_exists("isExplain", $r)) $data->isExplain = (string) $r["isExplain"];
      return $data->save();
    }

    static function updateIndexQuestion($user, $id, $q)
    {
      $data = YclassSession::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
        $q->where("user", "=", $user);
      })->where([
        ["id", "=", $id]
      ])->first();
      if (!$data) return false;
      $totalQ = Question::where([
        ["yclass", "=", $data->yclass]
      ])->select('id')->count();
      $totalIndexQ = $totalQ-1;
      if (is_bool($q)) {
        $nextIndex = $data->index_question+1;
      }
      else {
        $nextIndex = $q;
      }
      if ($totalIndexQ < $nextIndex) {
        return (object) [
          "status" => false,
          "errCode" => "end",
          "errMsg" => "theres no question anymore in this yclass"
        ];
      }
      $data->index_question = $nextIndex;
      return (object) [
        "status" => $data->save(),
        "index" => $nextIndex
      ];
    }
}
