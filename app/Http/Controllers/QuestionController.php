<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\YClass;
use Validator;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'id_yclass' => 'required|numeric',
        'question' => 'required',
        'media' => 'mimetypes:video/*,image/*,audio/*|max:10240',
        'correct' => 'required|in:a1,a2,a3,a4,a5,a6',
      ]);
      if ($validator->fails()) {
        return $validator->errors();
      }
      $classBelonging = YClass::select("id")->where([
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
        $mediaType = $media->getMimeType();
        $mediaType = explode('/', $mediaType)[0];
        $mediaName = date('dmyhis').$media->getClientOriginalName();
        $media->move('media/', $mediaName);
        $inputMediaToDb = json_encode([$mediaName, $mediaType]);
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
}
