<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
  public function index()
  {

    $uid = Auth::user()->id;


    // need to create this tables
    // $indexer              = 0;
    // $users                = users::find($uid);

    // if ($users['status'] != 4) $indexer = 1000;
    // $user_indexer         = user_indexer::where('uid', $uid);

    // if ($user_indexer) $indexer = $user_indexer['indexer'];
    // $user_uses            = user_uses::all_sql("where `uid`= '$uid' AND `type` = '1'");

    // $indexer_point        = 0;
    // if (isset($user_uses[0]['use'])) $indexer_point = $user_uses[0]['use'];
    // $user_indexer_points  = $indexer - $indexer_point;




    // Get lifetime plans with price not null and status = 1
    $lifetimePlans = DB::table('plans')
      ->where('type', 'lifetime')
      ->where('status', 1)
      ->get();

    // Get all monthly plans
    $monthlyPlans = DB::table('plans')
      ->where('type', 'monthly')
      ->where('price', '!=', 0)
      ->where('status', 1)
      ->get();

    // Get third party types with status = 1
    // $thirdPartyTypes = DB::table('third_party_type')
    //   ->where('status', '1')
    //   ->get();

    // Check if user is authenticated
    $isAuthenticated = Auth::check();

    return view('home', compact(
      'lifetimePlans',
      'monthlyPlans',
      // 'thirdPartyTypes',
      'isAuthenticated'
    ));
  }

  public function Home()
  {
    return $this->index();
  }
}
