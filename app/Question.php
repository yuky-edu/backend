<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
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
    'correct'
  ];

  public function yclass()
  {
    return $this->belongsTo("App\YClass", "yclass", "id");
  }

  static function store(
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
    return Question::create([
      'yclass' => $yclass,
      'question' => $question,
      'media' => $media,
      'a1' => $a1,
      'a2' => $a2,
      'a3' => $a3,
      'a4' => $a4,
      'a5' => $a5,
      'a6' => $a6,
      'correct' => $correct
    ]);
  }

  static function destroyMyQ($user, $id)
  {
    $data = Question::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
      $q->where('user', '=', $user);
    })->where([
      ['id', '=', $id]
    ])->first();
    if (!$data) return false;
    return $data->delete();
  }

  static function getQuestionsByIdYClass($user, $id_yclass)
  {
    $data = Question::with('yclass:id,user')->whereHas('yclass', function($q) use($user, $id_yclass) {
      $q->where('user', '=', $user);
      $q->where('id', '=', $id_yclass);
    })->get();
    if (!$data) return false;
    return $data;
  }

  static function getQuestionsById($user, $id)
  {
    $data = Question::with('yclass:id,user')->whereHas('yclass', function($q) use($user) {
      $q->where('user', '=', $user);
    })->where([
      ['id', '=', $id]
    ])->first();
    if (!$data) return false;
    return $data;
  }

  protected $hidden = [
    'created_at', 'updated_at'
  ];
}