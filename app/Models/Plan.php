<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

  protected $table = 'plans';

  protected $fillable = [
    'name',
    'price',
    'price_offer',
    'type',
    'indexer',
    'bg_indexer',
    'backlinks',
    'indexer_list',
    'bg_indexer_list',
    'trial',
    'status',
  ];

  protected $guarded = [];
}
