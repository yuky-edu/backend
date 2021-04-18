<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
  protected $fillable = [
    'yclass',
    'question',
    'media',
    'a1',
    'a2',
    'a3',
    'a4',
    'a5',
    'a6',
    'correct',
    'theory',
    'type'
  ];

  public function yclass()
  {
    return $this->belongsTo("App\Yclass", "yclass", "id");
  }

  static function store_question(
    $yclass,
    $question,
    $media,
    $a1,
    $a2,
    $a3,
    $a4,
    $a5,
    $a6,
    $correct
  ) {
    return Entity::create([
      'yclass' => $yclass,
      'question' => $question,
      'media' => $media,
      'a1' => $a1,
      'a2' => $a2,
      'a3' => $a3,
      'a4' => $a4,
      'a5' => $a5,
      'a6' => $a6,
      'correct' => $correct,
      'type' => 'q'
    ]);
  }

  static function store_theory(
    $yclass,
    $theory,
    $media
  ) {
    return Entity::create([
      'yclass' => $yclass,
      'theory' => $theory,
      'media' => $media,
      'type' => 't'
    ]);
  }

  static function update_question(
    $user,
    $id_q,
    $question,
    $media,
    $a1,
    $a2,
    $a3,
    $a4,
    $a5,
    $a6,
    $correct
  ) {
    $data = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
      $q->where("user", "=", $user);
    })->where([
      ["id", "=", $id_q],
      ["type", "=", 'q']
    ])->first();
    if (!$data) return false;
    $data->question = $question;
    if ($media !== null) {
      $data->media = $media;
    }
    $data->a1 = $a1;
    $data->a2 = $a2;
    $data->a3 = $a3;
    $data->a4 = $a4;
    $data->a5 = $a5;
    $data->a6 = $a6;
    $data->correct = $correct;
    return $data->save();
  }

  static function update_theory(
    $user,
    $id_q,
    $theory,
    $media
  ) {
    $data = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
      $q->where("user", "=", $user);
    })->where([
      ["id", "=", $id_q],
      ["type", "=", "t"]
    ])->first();
    if (!$data) return false;
    $data->theory = $theory;
    if ($media !== null) {
      $data->media = $media;
    }
    return $data->save();
  }

  static function destroyMyE($user, $id)
  {
    $data = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
      $q->where('user', '=', $user);
    })->where([
      ['id', '=', $id]
    ])->first();
    if (!$data) return false;
    return $data->delete();
  }

  static function getEntityByIdYClass($user, $id_yclass)
  {
    $data = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($user, $id_yclass) {
      $q->where('user', '=', $user);
      $q->where('yclass', '=', $id_yclass);
    })->get();
    $data = json_decode(json_encode($data),true);
    $data = array_map('array_filter',$data);
    $data = array_map(function ($data){
            if(!in_array(null,$data))
                return $data;
            }, $data);
    $data = array_filter($data);
    return $data;
  }

  static function getSingleMyEntity($user, $id)
  {
    $data = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
      $q->where('user', '=', $user);
    })->where('id', '=', $id)->first();
    if (!$data) return false;
    $data = json_decode(json_encode($data),true);
    $data = array_filter($data);
    return $data;
  }
}
