<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

  use HasFactory;

  protected $fillable = ['uid', 'subject', 'status', 'priority', 'closed_at', 'category_id', 'external_user_id'];

  public function user()
  {
    // return $this->belongsTo(User::class);
    return $this->belongsTo(User::class, 'uid');
  }
  public function messages()
  {
    return $this->hasMany(TicketMessage::class)->latest();
  }
}
