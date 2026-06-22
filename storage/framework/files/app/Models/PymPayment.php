<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PymPayment extends Model
{
  protected $table = 'pym_payments';

  protected $guarded = [];

  // Specify which fields are mass assignable
  protected $fillable = [
    'uid',
    'plan_id',
    'gateway_id',
    'product',
    'txn',
    'amount',
    'currency_id',
    'source_details',
    'subscription_id',
    'refref',
    'status',
    'payment_hash',
    'metadata',
    'completed_at',
  ];

  protected $casts = [
    'source_details' => 'array',
  ];

  public function plan(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Plan::class);
  }

  public function subscription()
  {
    return $this->belongsTo(PymSubscription::class, 'subscription_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'uid', 'id');
  }

  public function gateway()
  {
    return $this->belongsTo(PymGateway::class);
  }
}
