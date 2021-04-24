<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yclass extends Model
{
  protected $fillable = [
    'user',
    'yclass_category',
    'code',
    'title',
    'description'
  ];

  public function user(){
  	return $this->belongsTo('App\User', 'user', 'id');
  }
  public function entities()
  {
    return $this->hasMany("App\Entity", "yclass", "id");
  }
  public function last_session()
  {
    return $this->hasOne("App\YclassSession", "yclass", "id")->latest();
  }
  public function yclass_category(){
  	return $this->belongsTo('App\YclassCategory', 'yclass_category', 'id');
  }
  static function store($users_id, $yclass_categories_id, $code, $title, $description) {
    return Yclass::create([
      'user' => $users_id,
      'yclass_category' => $yclass_categories_id,
      'code' => $code,
      'title' => $title,
      'description' => $description
    ]);
  }

  static function single($where = [], $select = []) {
    return Yclass::with("user", "yclass_category")->select(...$select)->where($where)->first();
  }

  static function singleWithQ($where = [], $select = []) {
    return Yclass::with("yclass_category", "entities")->select(...$select)->where($where)->first();
  }

  static function getAll($where = []) {
    return Yclass::with("yclass_category", "last_session")->withCount("entities")->where($where)->get();
  }

  static function updateData($id, $users_id, $info) {
    try {
      $data = YClass::where([
        ["user", "=", $users_id],
        ["id", "=", $id]
      ])->first();
      foreach ($info as $key => $value) {
        $data[$key] = $value;
      }
      return $data->save();
    } catch (\Exception $e) {
      return response()->json([
        "status" => false,
        "errCode" => "-",
        "errMsg" => $e->getMessage()
      ]);
    }
  }

  static function deleteClass($id, $user) {
    $data = Yclass::select("id")->where([
      ["id", "=", $id],
      ["user", "=", $user]
    ])->first();
    if (!$data) return false;
    return $data->delete();
  }

  protected $hidden = [
    'created_at', 'updated_at'
  ];
}
