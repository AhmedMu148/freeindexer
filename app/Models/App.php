<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
  protected $table = 'app';

  protected $fillable = [
    'uid',
    'payment_id',
    'key',
    'date',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (empty($model->uid)) {
        $model->uid = (string) \Illuminate\Support\Str::uuid();
      }
    });
  }
}
