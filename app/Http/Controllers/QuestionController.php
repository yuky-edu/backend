<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Yclass;
use Validator;

class QuestionController extends Controller
{
    public function uploadMedia($media)
    {
      $mediaType = $media->getMimeType();
      $mediaType = explode('/', $mediaType)[0];
      $mediaName = date('dmyhis').$media->getClientOriginalName();
      $media->move('media/', $mediaName);
      return (object) [
        "mediaName" => $mediaName,
        "mediaType" => $mediaType
      ];
    }
    public function store(Request $request)
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
        $uploadMedia = $this->uploadMedia($media);
        $inputMediaToDb = json_encode([$uploadMedia->mediaName, $uploadMedia->mediaType]);
      }
      $stored = Question::store(
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

    public function update(Request $request, $id)
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
        $uploadMedia = $this->uploadMedia($media);
        $inputMediaToDb = json_encode([$uploadMedia->mediaName, $uploadMedia->mediaType]);
      }
      $updated = Question::updateQ(
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

    public function destroyMyQ(Request $request, $id)
    {
      $deleted = Question::destroyMyQ($request->get('myid'), $id);
      $status = $deleted ? true : false;
      return response()->json([
        "status" => $status
      ]);
    }

    public function getQuestionsByIdYClass(Request $request, $id_yclass)
    {
      return Question::getQuestionsByIdYClass($request->get('myid'), $id_yclass);
    }

    public function getQuestionsById(Request $request, $id)
    {
      return Question::getQuestionsById($request->get('myid'), $id);
    }

    public function countMyQuestion(Request $request)
    {
      $count = Question::with('yclass:id,user')->whereHas('yclass', function($q) use($request){
        $q->where('user', '=', $request->get('myid'));
      })->select('id')->count();
      return response()->json([
        "status" => true,
        "count" => $count
      ]);
    }
}
