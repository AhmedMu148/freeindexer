<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketSeeder extends Seeder
{
  public function run(): void
  {
    // إعدادات سريعة تقدر تغيّرها
    $USERS_TO_SEED         = 5;   // عدد المستخدمين التجريبيين لو مفيش يوزرز
    $TICKETS_PER_USER      = 5;   // عدد التذاكر لكل يوزر
    $EXTRA_MESSAGES_MAX    = 4;   // أقصى عدد رسائل إضافية بعد الأولى

    // استخدم يوزرز موجودين؛ ولو مفيش، أنشئ جدد
    $users = User::query()->inRandomOrder()->limit($USERS_TO_SEED)->get();
    if ($users->isEmpty()) {
      $users = User::factory()->count($USERS_TO_SEED)->create();
    }

    $users->each(function (User $user) use ($TICKETS_PER_USER, $EXTRA_MESSAGES_MAX) {
      // لكل يوزر: أنشئ N تذاكر مملوكة له
      Ticket::factory()
        ->count($TICKETS_PER_USER)
        ->for($user) // يربط user_id
        ->create()
        ->each(function (Ticket $ticket) use ($user, $EXTRA_MESSAGES_MAX) {
          // الرسالة الأولى (بعد إنشاء التذكرة بشوية دقائق)
          $firstAt = $ticket->created_at->copy()->addMinutes(rand(1, 90));

          TicketMessage::factory()
            ->for($ticket)
            ->for($user) // نفس صاحب التذكرة
            ->create([
              'created_at' => $firstAt,
              'updated_at' => $firstAt,
              'body'       => 'أول رسالة في التذكرة: ' . fake()->sentence(8),
            ]);

          // رسائل إضافية (0..MAX) من نفس المستخدم
          $extraCount = rand(0, $EXTRA_MESSAGES_MAX);
          if ($extraCount > 0) {
            TicketMessage::factory()
              ->count($extraCount)
              ->for($ticket)
              ->for($user)
              ->create();
          }
        });
    });
  }
}
