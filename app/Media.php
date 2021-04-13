<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
  protected $fillable = [
    'file',
    'type'
  ];

  public function store($file, $type)
  {
    return Media::create([
      "file" => $file,
      "type" => $type
    ]);
  }
}
