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

    public function getByIdEntityAndSession(Request $request, $id_entity, $id_session)
    {
      $user = $request->get('myid');
      $datas = PlayerAnswer::with('entity_correct', 'player_info:id,name,avatar,yclass_session', 'entity_correct.yclass:id,user')->whereHas('entity_correct.yclass', function($q) use($user) {
        $q->where('user', '=', $user);
      })->whereHas('player_info', function($q) use($id_session) {
        $q->where('yclass_session', '=', $id_session);
      })->orderBy('id', 'ASC')->where([
        ["entity", "=", $id_entity]
      ])->get();
      if ($datas) {
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
      }
      return response()->json([
        "status" => true,
        "data" => $datas
      ]);
    }

    public function getIdPlayerWhoAnsweredByIdEntity(Request $request, $id_entity, $id_session)
    {
      $datas = PlayerAnswer::with('player_info:id,yclass_session')->whereHas('player_info', function($q) use($id_session) {
        $q->where('yclass_session', '=', $id_session);
      })->where([
        ["entity", "=", $id_entity]
      ])->select('id', 'player')->get();
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

    public function getMyAnswerBySession(Request $request, $id_session)
    {
      $user = $request->get('myid');
      $datas = PlayerAnswer::with('player_info:id,yclass_session', 'entity_correct')->whereHas('player_info', function ($q) use($id_session) {
        $q->where('yclass_session', '=', $id_session);
      })->where([
        ["player", "=", $user]
      ])->get();
      foreach($datas as $data) {
        if ($data->entity_correct->correct == $data->answer) {
          $data->correct = true;
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
}
