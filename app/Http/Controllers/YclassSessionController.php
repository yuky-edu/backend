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
      $checkSession = YclassSession::select("id")->where([
        ["yclass", "=", $request->id_yclass],
        ["played", "=", "1"]
      ])->orderBy('id', 'DESC')->first();
      if ($checkSession) {
        return response()->json([
          "status" => false,
          "errCode" => "sessionIsPlayed",
          "errMsg" => "cannot create this session. yclass session already running on id ".$checkSession->id,
          "id_session" => $checkSession->id
        ]);
      }
      $stored = YclassSession::store($request->id_yclass);
      $status = $stored ? true : false;
      return response()->json([
        "status" => $status
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
        'played' => 'in:0,1',
        'isExplain' => 'in:0,1'
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
     $updated = YclassSession::updateSession($request->get('myid'), $id, $request->all());
     return response()->json([
       "status" => $updated
     ]);
    }

    public function updateIndexQuestion(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'nextQuestion' => 'required'
      ]);
      $updated = YclassSession::updateIndexQuestion($request->get('myid'), $id, $request->nextQuestion);
      return response()->json($updated);
    }
}