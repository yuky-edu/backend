<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerAnswer extends Model
{
  protected $fillable = [
    'player',	'entity',	'answer'
  ];

  public function player()
  {
    return $this->belongsTo('App\Player', 'player', 'id');
  }

  public function entity_correct()
  {
    return $this->belongsTo('App\Entity', 'entity', 'id')->select('id', 'correct', 'yclass');
  }

  static function store($player, $entity, $answer)
  {
    return PlayerAnswer::updateOrCreate(['player' => $player, 'entity' => $entity], [
      "answer" => $answer
    ]);
  }

  static function destroyAnswer($player, $id)
  {
    $data = PlayerAnswer::where([
      ["player", "=", $player],
      ["id", "=", $id]
    ])->select('id')->first();
    if (!$data) return false;
    return $data->delete();
  }

  protected $hidden = [
    'created_at', 'updated_at'
  ];
}
