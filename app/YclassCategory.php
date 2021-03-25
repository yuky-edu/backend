<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YclassCategory extends Model
{
  protected $fillable = [
    'name'
  ];

  static function getAll() {
    return YclassCategory::orderBy("name", "ASC")->get();
  }

  protected $hidden = [
    'created_at', 'updated_at'
  ];
}
