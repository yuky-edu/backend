<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YclassCategory extends Model
{
  protected $fillable = [
    'name', 'image'
  ];

  static function getAll() {
    $data = YclassCategory::get();
    foreach ($data as $value) {
      $value->image = config('app.url').'/img/category/'.$value->image;
    }
    return $data;
  }

  protected $hidden = [
    'created_at', 'updated_at'
  ];
}
