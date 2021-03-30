<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DebugController extends Controller
{
    public function deleteAllUser()
    {
      $deleted = DB::table('users')->delete();
      return response()->json([
        "deleted" => $deleted
      ]);
    }
}
