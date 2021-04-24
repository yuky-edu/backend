<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Player;
use App\Yclass;

class PlayerController extends Controller
{
    // Host
    public function getPlayersBySession(Request $request, $id_session)
    {
      $user = $request->get('myid');
      $data = Player::with('yclass_session:id,yclass', 'yclass_session.yclass:id,user')->whereHas('yclass_session.yclass', function($q) use($user) {
        $q->where('user', '=', $user);
      })->where([
        ["yclass_session", "=", $id_session]
      ])->select('id', 'yclass_session', 'name', 'photo')->get();
      return response()->json([
        "status" => true,
        "data" => $data
      ]);
    }

    // Play
    public function register($name, $session_id)
    {
      $photo = ['avatar1.png', 'avatar2.png', 'avatar3.png', 'avatar4.png', 'avatar5.png'];
      $photo = $photo[array_rand($photo)];
      $stored = Player::register($session_id, $name, $photo);
      $status = $stored ? true : false;
      return $stored;
    }

    public function kick(Request $request, $id)
    {
      $removed = Player::removeById($request->get('myid'), $id);
      return response()->json([
        "status" => $removed
      ]);
    }

    public function joinClass(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'code' => 'required',
        'playerName' => 'required'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      $code = $request->code;
      $dataYclass = Yclass::where([
        ['code', '=', $code]
      ])->with('last_session', 'yclass_category')->first();
      switch ($dataYclass->last_session->status) {
        case 'off':
        return response()->json([
          "status" => false,
          "errCode" => "off",
          "errMsg" => "this class session is off"
        ]);
          break;
        case 'playing':
          return response()->json([
            "status" => false,
            "errCode" => "playing",
            "errMsg" => "this class session is already played"
          ]);
          break;
      }
      if ($dataYclass) {
        $player = $this->register($request->playerName, $dataYclass->last_session->id);
        $player->photo = env('APP_URL').'/img/avatar/'.$player->photo;
        return response()->json([
          "status" => true,
          "data" => [
            "yclass" => $dataYclass,
            "player" => $player
          ]
        ]);
      }
      return response()->json([
        "status" => false
      ]);
    }

    public function updatePlayer(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'photo' => 'mimetypes:image/*'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      if ($request->has('photo')) {
        $photoFile = $request->photo;
        $photo = date('dmyhis').$photoFile->getClientOriginalName();
        $photoFile->move('img/avatar', $photo);
        $request->merge(["photoName" => $photo]);
      }
      $updated = Player::updatePlayer($request->get('myid'), $request->all());
      return response()->json([
        "status" => $updated
      ]);
    }

    public function countMyFriend(Request $request)
    {
      $count = Player::where([
        ["yclass_session", "=", $request->get('myidsession')]
      ])->select('id')->get()->count();
      return response()->json([
        "status" => true,
        "total" => $count
      ]);
    }
}
