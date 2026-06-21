<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PymGateway extends Model
{
  protected $guarded = [];
  protected $casts = [
    'countries' => 'array',
    'data'      => 'array',
    'details'   => 'array',
    'item_show' => 'boolean',
    'one_time'  => 'boolean',
    'subs'      => 'boolean',
  ];
}
