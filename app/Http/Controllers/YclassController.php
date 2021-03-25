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
      $request->description,
      '0'
    );
    $status = $stored ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function getById(Request $request, $id) {
    $where = [
      ["id", "=", $id],
      ["user", "=", $request->get("myid")]
    ];
    return response()->json([
      "status" => true,
      "data" => Yclass::single($where)
    ]);
  }

  public function myclass(Request $request) {
    $where = [
      ["user", "=", $request->get("myid")]
    ];
    return response()->json([
      "status" => true,
      "data" => Yclass::getAll($where)
    ]);
  }

  public function destroy(Request $request, $id) {
    $deleted = Yclass::destroy($id, $request->get("myid"));
    $status = $deleted ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }
}
