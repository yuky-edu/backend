<?php

namespace App\Http\Controllers;

use App\YclassCategory;
use Illuminate\Http\Request;

class YclassCategoryController extends Controller
{
    public function index() {
      return YclassCategory::getAll();
    }
}
