<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PlayerAnswer;
use Validator;

class PlayerAnswerController extends Controller
{
    public function scoreFormula($position, $total)
    {
      return ($total/$position * 1000);
    }

    public function getByIdEntity(Request $request, $id)
    {
      $user = $request->get('myid');
      $datas = PlayerAnswer::with('entity_correct', 'player_info:id,name,avatar', 'entity_correct.yclass:id,user')->whereHas('entity_correct.yclass', function($q) use($user) {
        $q->where('user', '=', $user);
      })->where([
        ["entity", "=", $id]
      ])->get();
      $pos = 1;
      foreach($datas as $data) {
        $data->player_info->avatar = env('APP_URL').'/img/avatar/'.$data->player_info->avatar;
        if ($data->entity_correct->correct == $data->answer) {
          $data->score = $this->scoreFormula($pos, $datas->count());
          $data->correct = true;
          $pos++;
        }
        else {
          $data->correct = false;
        }
      }
      return response()->json([
        "status" => true,
        "data" => $datas
      ]);
    }

    // Plays
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'entity' => 'required',
        'answer' => 'required|in:a1,a2,a3,a4,a5,a6',
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      $user = $request->get('myid');
      $stored = PlayerAnswer::store($user, $request->entity, $request->answer);
      $status = $stored ? true : false;
      if ($stored) {
        return response()->json([
          "status" => $status,
          "data" => $stored
        ]);
      }
    }

    public function destroyAnswer(Request $request, $id)
    {
      $user = $request->get('myid');
      $deleted = PlayerAnswer::destroyAnswer($user, $id);
      return response()->json([
        "status" => $deleted
      ]);
    }

    public function getByPlayerAndEntity(Request $request, $id)
    {
      $user = $request->get('myid');
      $selected = PlayerAnswer::getByPlayerAndEntity($user, $id);
      return response()->json([
        "status" => true,
        "data" => $selected
      ]);
    }
}
