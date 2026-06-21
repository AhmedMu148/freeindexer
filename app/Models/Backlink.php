<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{

  use HasFactory;

  protected $table = 'backlinks_orders';

  protected $fillable = [
    'uid',
    'ses_id',
    'project_name',
    'links',
    'keywords',
    'qty',
    'report',
    'status_id',
  ];

  public function getUrlsCountAttribute()
  {
    if (is_array($this->links)) {
      return count($this->links);
    }
    $links = preg_split('/\r\n|\r|\n|,/', trim($this->links));
    return count(array_filter($links, fn($u) => !empty($u)));
  }

  public function getKwsCountAttribute()
  {
    if (is_array($this->links)) {
      return count($this->links);
    }
    $links = preg_split('/\r\n|\r|\n|,/', trim($this->links));
    return count(array_filter($links, fn($u) => !empty($u)));
  }

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
