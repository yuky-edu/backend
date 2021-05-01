<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YclassSession;
use App\Yclass;
use Validator;

class YclassSessionController extends Controller
{
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'id_yclass' => 'required'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      $classBelonging = Yclass::select("id")->where([
        ["id", "=", $request->id_yclass],
        ["user", "=", $request->get('myid')]
      ])->first();
      if (!$classBelonging) {
        return response()->json([
          "status" => false,
          "errCode" => "notFound",
          "errMsg" => "yclass with this id not found or you cant access this yclass"
        ]);
      }
      $checkSession = YclassSession::select("id", "ws_channel")->where([
        ["yclass", "=", $request->id_yclass],
        ["status", "!=", "off"]
      ])->orderBy('id', 'DESC')->first();
      if ($checkSession) {
        return response()->json([
          "status" => true,
          "data" => [
            "id" => $checkSession->id,
            "ws_channel" => $checkSession->ws_channel
          ]
        ]);
      }
      $stored = YclassSession::store($request->id_yclass);
      $status = $stored ? true : false;
      return response()->json([
        "status" => $status,
        "data" => [
          "id" => $stored->id,
          "ws_channel" => $stored->ws_channel
        ]
      ]);
    }

    public function getSingle(Request $request)
    {
      if (!$request->query->get('id_session') && !$request->query->get('id_yclass')) {
        return response()->json([
          "status" => false,
          "errCode" => "paramsNeeded",
          "errMsg" => "please include params: id_session or id_yclass"
        ]);
      }
      $where = [];
      if ($request->query->get('id_session')) {
        array_push($where, [
          "id", "=", $request->query->get('id_session')
        ]);
      }
      if ($request->query->get('id_yclass')) {
        array_push($where, [
          "yclass", "=", $request->query->get('id_yclass')
        ]);
      }
      $data = YclassSession::single($request->get('myid'), $where);
      return response()->json([
        "status" => true,
        "data" => $data
      ]);
    }

    public function updateSession(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'status' => 'in:off,wait,on_mode_block,on_mode_open'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
     $updated = YclassSession::updateSession($request->get('myid'), $id, $request->all());
     return response()->json([
       "status" => $updated
     ]);
    }

    public function updateAnsweredEntity(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'answered_entity' => 'required'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
     $updated = YclassSession::updateAnsweredEntity($request->get('myid'), $id, $request->answered_entity);
     return response()->json([
       "status" => $updated->status,
       "answered_entity" => $updated->answered_entity
     ]);
    }

    public function updateIndexEntity(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'nextEntity' => 'required'
      ]);
      $updated = YclassSession::updateIndexEntity($request->get('myid'), $id, $request->nextEntity);
      return response()->json($updated);
    }

    // Play
    public function playSingleSession(Request $request)
    {
      $data = YclassSession::playSingleSession($request->get('myidsession'));
      return response()->json([
        "status" => true,
        "data" => $data
      ]);
    }
}
