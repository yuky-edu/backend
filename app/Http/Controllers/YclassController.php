<?php

namespace App\Http\Controllers;

use App\Yclass;
use Illuminate\Http\Request;
use Validator;

class YclassController extends Controller
{
  public function store(Request $request) {
    $validator = Validator::make($request->all(), [
      'category' => 'required|numeric',
      'title' => 'required',
      'code' => 'required|unique:yclasses,code',
      'description' => 'required'
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }
    $stored = Yclass::store(
      $request->get("myid"),
      $request->category,
      $request->code,
      $request->title,
      $request->description
    );
    $status = $stored ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function getMyClassSingle(Request $request) {
    $where = [
      ["user", "=", $request->get("myid")]
    ];
    if ($request->query->get('id')) {
      array_push($where, ["id", "=", $request->query->get('id')]);
    }
    if ($request->query->get('code')) {
      array_push($where, ["code", "=", $request->query->get('code')]);
    }
    $data = Yclass::single($where);
    $data->category->image = config('app.url').'/img/category/'.$data->category->image;
    return response()->json([
      "status" => true,
      "data" => $data
    ]);
  }

  public function myclass(Request $request) {
    $where = [
      ["user", "=", $request->get("myid")]
    ];
    $data = Yclass::getAll($where);
    foreach ($data as $value) {
      $value->category->imageurl = config('app.url').'/img/category/'.$value->category->image;
    }
    return response()->json([
      "status" => true,
      "data" => $data
    ]);
  }

  public function destroy(Request $request, $id) {
    $deleted = Yclass::deleteClass($id, $request->get("myid"));
    $status = $deleted ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function generateCode()
  {
    function generateRandomString($length = 5) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }
    $random = generateRandomString();
    $data = Yclass::select("id")->where([
      ["code", "=", $random]
    ])->first();
    if ($data) {
      $random = generateRandomString();
    }
    return response()->json([
      "status" => true,
      "code" => $random
    ]);
  }

  public function updateMyClassSingle(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'code' => 'unique:yclasses,code',
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }
    $updated = Yclass::updateData($id, $request->get("myid"), $request->all());
    return response()->json([
      "status" => $updated
    ]);
  }

  // Play
  public function getInfoClassByCode($code)
  {
    $data = Yclass::where([
      ["code", "=", $code]
    ])->select('id', 'title', 'description')->first();
    return response()->json([
      "status" => true,
      "data" => $data
    ]);
  }
}
