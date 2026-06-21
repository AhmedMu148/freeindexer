<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserDataServiceProvider extends ServiceProvider
{
  public function boot()
  {
    View::composer('*', function ($view) {

      $userData           = null;
      $userIndexerPoints  = 0;
      $isAuthenticated    = Auth::check();

      if ($isAuthenticated) {

        $user       = Auth::user();
        $uid        = $user->id;
        $status_id  = $user->status_id;

        $userData = [
          'user'            => $user,
          'uid'             => $uid,
          'indexer_points'  => $this->getUserIndexerPoints($uid, $status_id),
          'user_status'     => $user->status_id,
        ];

        $userIndexerPoints = $userData['indexer_points'];
      } else {
        $userIndexerPoints = $this->getDailyIndexerPoints();
      }

      $view->with([
        'userData'          => $userData,
        'userIndexerPoints' => $userIndexerPoints,
        'isAuthenticated'   => $isAuthenticated
      ]);
    });
  }

  private function getUserIndexerPoints($uid, $status_id)
  {

    if ($status_id == 4) return 0;

    $indexerPoints = $indexerUsed = $indexerAvailablePoints = 0;

    // Get indexer points data
    $indexerPointsData = DB::table('indexer_points')->where('uid', $uid)->first();
    if ($indexerPointsData) {
      $indexerPoints          = $indexerPointsData ? $indexerPointsData->points : 0;
      $indexerUsed            = $indexerPointsData ? $indexerPointsData->used : 0;
    } else {
      // $isVerified = DB::table('users')
      //   ->where('id', $uid)
      //   ->whereNotNull('email_verified_at')
      //   ->exists();
      // if ($isVerified) {
      $orderData = DB::table('orders')->where('uid', $uid)->where('status_id', 2)->first();
      if (!$orderData) {
        $indexerAvailablePoints = 1000;
        $now              = Carbon::now();
        $x                = [];
        $x['uid']         = $uid;
        $x['points']      = $indexerAvailablePoints;
        $x['used']        = 0;
        $x['created_at']  = $now;
        $x['updated_at']  = $now;
        DB::table('indexer_points')->insert($x);
        $indexerPoints    = $indexerAvailablePoints;
        $indexerUsed      = 0;
      }
      // }
    }

    $indexerAvailablePoints = $indexerPoints - $indexerUsed;
    return $indexerAvailablePoints;
  }

  // private function getUserPlan($uid)
  // {
  //   return DB::table('plans')
  //     ->join('plans', 'user_plans.plan_id', '=', 'plans.id')
  //     ->where('user_plans.uid', $uid)
  //     ->where('user_plans.status', 1)
  //     ->select('plans.*')
  //     ->first();
  // }

  private function getDailyIndexerPoints()
  {

    $points     = 50;
    $ipaddress  = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
      $ipaddress = 'UNKNOWN';

    // get from ip_address table
    $date_time  = gmdate('Y-m-d');
    $now        = Carbon::now();
    $ip_address = DB::table('ip_addresses')->where('ip', $ipaddress)->whereDate('created_at', $date_time)->first();
    if (!$ip_address) {
      // update ip_address table
      $x                = [];
      $x['ip']          = $ipaddress;
      $x['points']      = $points;
      $x['created_at']  = $now;
      $x['updated_at']  = $now;
      DB::table('ip_addresses')->insert($x);
    } else {
      $points = $ip_address->points - $ip_address->used;
    }
    return $points;
  }

  public function register()
  {
    //
  }
}
