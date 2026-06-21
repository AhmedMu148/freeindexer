<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indexer extends Model
{
  use HasFactory;

  protected $table = 'indexer_orders';

  protected $fillable = [
    'uid',
    'date',
    'urls_count',
    'status_id',
  ];

  protected $casts = [
    'urls_count' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function scopePending($query)
  {
    return $query->where('status', 'pending');
  }

  public function scopeProcessing($query)
  {
    return $query->where('status', 'processing');
  }

  public function scopeCompleted($query)
  {
    return $query->where('status', 'completed');
  }

  public function scopeFailed($query)
  {
    return $query->where('status', 'failed');
  }
}
