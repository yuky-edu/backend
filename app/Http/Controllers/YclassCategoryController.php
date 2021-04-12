<?php

namespace App\Http\Controllers;

use App\YclassCategory;
use Illuminate\Http\Request;

class YclassCategoryController extends Controller
{
    public function index() {
      return response()->json([
        "status" => true,
        "data" => YclassCategory::getAll()
      ]);
    }
}
