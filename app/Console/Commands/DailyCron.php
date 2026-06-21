<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use App\Models\bg_indexer_points;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyCron extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:daily-cron';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Run daily cron tasks';

  /**
   * Execute the console command.
   */
  public function handle()
  {

    $today = Carbon::today()->toDateString();

    // update order status and start date //
    // chnage order status from active to expired where end date is less than today //
    $date = gmdate('ymd');
    Order::where('status_id', 2)
      ->whereDate('end', '<', now())
      ->where('end', '!=', '0000-00-00')
      ->update(['status_id' => 3]);

    // $orders = Order::where('status_id', 2)->groupBy('uid')->get();

    // delete all rows in indexer_points, user_bg_indexer, backlinks daily //
    $tables = ['indexer_points', 'bg_indexer_points', 'backlinks_points'];
    foreach ($tables as $table) {
      DB::table($table)->whereDate('created_at', '!=', now()->toDateString())->delete();
    }

    // update users indexer points based on active orders //
    $query  = Order::query()->where('status_id', 2)->where('flag', 0);
    $rows   = $query->select([
      'uid',
      DB::raw('COALESCE(SUM(indexer), 0)    AS indexer_total'),
      DB::raw('COALESCE(SUM(bg_indexer), 0) AS bg_indexer_total'),
      DB::raw('COALESCE(SUM(backlinks), 0)  AS backlinks_total'),
    ])
      ->groupBy('uid')
      ->get();
    if ($rows->isNotEmpty()) {
      foreach ($rows as $row) {
        $uid         = $row->uid;
        $indexer     = $row->indexer_total;
        $bg_indexer  = $row->bg_indexer_total;
        $backlinks   = $row->backlinks_total;

        if ($indexer > 0) {
          DB::table('indexer_points')->insert(['uid' => $uid, 'points' => $indexer, 'created_at' => now(), 'updated_at' => now()]);
        }

        if ($bg_indexer > 0) {
          DB::table('bg_indexer_points')->insert(['uid' => $uid, 'points' => $bg_indexer, 'created_at' => now(), 'updated_at' => now()]);
        }

        if ($backlinks > 0) {
          DB::table('backlinks_points')->insert(['uid' => $uid, 'points' => $backlinks, 'created_at' => now(), 'updated_at' => now()]);
        }

        // update flag in table order where uid = $uid
        DB::table('orders')
          ->where('uid', $uid)
          ->where('status_id', 2)
          ->update(['flag' => 1]);
      }
    }

    $this->info('Daily Cron ran at ' . $this->laravel->make('config')->get('app.timezone') . ' time.');
    return self::SUCCESS;
  }
}
