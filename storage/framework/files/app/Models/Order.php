<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  use HasFactory;

  protected $table = 'orders';

  protected $guarded = [];

  protected $casts = [
    'start' => 'datetime',
    'end'   => 'datetime',
  ];

  /**
   * المستخدم صاحب الطلب
   */
  public function user()
  {
    return $this->belongsTo(User::class, 'uid', 'id');
  }

  /**
   * عملية الدفع المرتبطة بالطلب
   */
  public function pymPayment()
  {
    return $this->belongsTo(PymPayment::class, 'payment_id');
  }

  /**
   * الخطة المرتبطة بالطلب
   */
  public function plan()
  {
    return $this->belongsTo(Plan::class, 'plan_id');
  }

  /**
   * الاشتراك المرتبط بالطلب (PayPal subscription أو CP)
   */
  public function pymSubscription()
  {
    return $this->belongsTo(PymSubscription::class, 'subscription_id');
  }
}
