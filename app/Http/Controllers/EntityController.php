<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entity;
use App\Yclass;
use Validator;

class EntityController extends Controller
{
  public function uploadMedia($media, $dir)
  {
    $mediaType = $media->getMimeType();
    $mediaType = explode('/', $mediaType)[0];
    $mediaName = date('dmyhis').$media->getClientOriginalName();
    $media->move('media/'.$dir.'/', $mediaName);
    return (object) [
      "mediaName" => $mediaName,
      "mediaType" => $mediaType
    ];
  }

  public function store_question(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'id_yclass' => 'required|numeric',
      'question' => 'required',
      'a1' => 'required',
      'a2' => 'required',
      'media' => 'mimetypes:video/*,image/*,audio/*|max:5000',
      'correct' => 'required|in:a1,a2,a3,a4,a5,a6',
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
    $media = $request->media;
    $inputMediaToDb = null;
    if (isset($media)) {
      $uploadMedia = $this->uploadMedia($media, 'question');
      $inputMediaToDb = json_encode([$uploadMedia->mediaName, $uploadMedia->mediaType]);
    }
    $stored = Entity::store_question(
      $request->id_yclass,
      $request->question,
      $inputMediaToDb,
      $request->a1,
      $request->a2,
      $request->a3,
      $request->a4,
      $request->a5,
      $request->a6,
      $request->correct
    );
    $status = $stored ? true : false;
    return response()->json([
      "status" => $status,
      "data" => $stored
    ]);
  }

  public function store_theory(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'id_yclass' => 'required|numeric',
      'theory' => 'required',
      'media' => 'mimetypes:video/*,image/*,audio/*|max:5000',
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
    $media = $request->media;
    $inputMediaToDb = null;
    if (isset($media)) {
      $uploadMedia = $this->uploadMedia($media, 'theory');
      $inputMediaToDb = json_encode([$uploadMedia->mediaName, $uploadMedia->mediaType]);
    }
    $stored = Entity::store_theory(
      $request->id_yclass,
      $request->theory,
      $inputMediaToDb
    );
    $status = $stored ? true : false;
    return response()->json([
      "status" => $status,
      "data" => $stored
    ]);
  }

  public function destroyMyE(Request $request, $id)
  {
    $deleted = Entity::destroyMyE($request->get('myid'), $id);
    $status = $deleted ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function getEntityByIdYClass(Request $request, $id_yclass)
  {
    $data = Entity::getEntityByIdYClass($request->get('myid'), $id_yclass);
    return response()->json([
      "status" => true,
      "data" => $data
    ]);
  }

  public function countMyQuestion(Request $request)
  {
    $count = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($request){
      $q->where('user', '=', $request->get('myid'));
    })->where('type', '=', 'q')->select('id')->count();
    return response()->json([
      "status" => true,
      "count" => $count
    ]);
  }

  public function countMyTheory(Request $request)
  {
    $count = Entity::with('yclass:id,user')->whereHas('yclass', function($q) use($request){
      $q->where('user', '=', $request->get('myid'));
    })->where('type', '=', 't')->select('id')->count();
    return response()->json([
      "status" => true,
      "count" => $count
    ]);
  }

  public function update_question(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'question' => 'required',
      'media' => 'mimetypes:video/*,image/*,audio/*|max:5000',
      'a1' => 'required',
      'a2' => 'required',
      'correct' => 'required|in:a1,a2,a3,a4,a5,a6',
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }
    $media = $request->media;
    $inputMediaToDb = null;
    if (isset($media)) {
      $uploadMedia = $this->uploadMedia($media, 'question');
      $inputMediaToDb = json_encode([$uploadMedia->mediaName, $uploadMedia->mediaType]);
    }
    $updated = Entity::update_question(
      $request->get('myid'),
      $id,
      $request->question,
      $inputMediaToDb,
      $request->a1,
      $request->a2,
      $request->a3,
      $request->a4,
      $request->a5,
      $request->a6,
      $request->correct
    );
    $status = $updated ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function update_theory(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'theory' => 'required',
      'media' => 'mimetypes:video/*,image/*,audio/*|max:5000',
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }
    $media = $request->media;
    $inputMediaToDb = null;
    if (isset($media)) {
      $uploadMedia = $this->uploadMedia($media, 'theory');
      $inputMediaToDb = json_encode([$uploadMedia->mediaName, $uploadMedia->mediaType]);
    }
    $updated = Entity::update_theory(
      $request->get('myid'),
      $id,
      $request->theory,
      $inputMediaToDb
    );
    $status = $updated ? true : false;
    return response()->json([
      "status" => $status
    ]);
  }

  public function getSingleMyEntity(Request $request, $id)
  {
    $data = Entity::getSingleMyEntity($request->get('myid'), $id);;
    return response()->json([
      "status" => true,
      "data" => $data
    ]);
  }
}