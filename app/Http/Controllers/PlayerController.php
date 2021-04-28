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
      ])->orderBy('id', 'DESC')->select('id', 'yclass_session', 'name', 'avatar')->get();
      foreach ($data as $value) {
        $value->avatar = env('APP_URL').'/img/avatar/'.$value->avatar;
      }
      return response()->json([
        "status" => true,
        "data" => $data
      ]);
    }

    public function kick(Request $request, $id)
    {
      $removed = Player::removeById($request->get('myid'), $id);
      return response()->json([
        "status" => $removed
      ]);
    }

    // Play
    public function register(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'id_session' => 'required',
        'avatar' => 'required'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      $photo = $request->avatar;
      $type = $request->avatar_type;
      if ($type == 'custom') {
        $photoName = date('dmyhis').$photo->getClientOriginalName();
        $photo->move('img/avatar', $photoName);
        $photo = $photoName;
      }
      $stored = Player::register($request->id_session, $request->name, $photo);
      $status = $stored ? true : false;
      $stored->avatar = env('APP_URL').'/img/avatar/'.$stored->avatar;
      return response()->json([
        "status" => $status,
        "data" => $stored
      ]);
    }

    public function joinClass(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'code' => 'required'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      $code = $request->code;
      $dataYclass = Yclass::where([
        ['code', '=', $code]
      ])->with('last_session', 'yclass_category')->first();
      if (!$dataYclass) {
        return response()->json([
          "status" => false,
          "errCode" => "notFound",
          "errMsg" => "class with this code not found"
        ]);
      }
      if ($dataYclass->last_session == null) {
        $status = 'off';
      }
      else {
        $status = $dataYclass->last_session->status;
      }
      switch ($status) {
        case 'off':
        return response()->json([
          "status" => false,
          "errCode" => "off",
          "errMsg" => "this class session is off"
        ]);
          break;
        case 'on_mode_block':
          return response()->json([
            "status" => false,
            "errCode" => "block",
            "errMsg" => "this class session is already played in block mode"
          ]);
          break;
      }
      if ($dataYclass) {
        // $player = $this->register($request->playerName, $dataYclass->last_session->id);
        return response()->json([
          "status" => true,
          "data" => [
            "yclass" => $dataYclass
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
        'avatar' => 'mimetypes:image/*'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      if ($request->has('avatar')) {
        $photoFile = $request->avatar;
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
        "total" => $count-1
      ]);
    }

    public function myInfo(Request $request)
    {
      $data = Player::where([
        ["id", "=", $request->get('myid')]
      ])->select('id', 'name', 'avatar')->first();
      if ($data) $data->avatar = env('APP_URL').'/img/avatar/'.$data->avatar;
      return response()->json([
        "status" => true,
        "data" => $data
      ]);
    }
}
