<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
  public function index()
  {
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

    return view('pricing', compact(
      'lifetimePlans',
      'monthlyPlans',
      // 'thirdPartyTypes',
      'isAuthenticated'
    ));
  }

  public function buyApp()
  {

    // Get app plans
    $appPlan = DB::table('plans')
      ->where('id', 8)
      ->where('status', 1)
      ->first();

    $price = $appPlan->price_offer != 0.00 ? $appPlan->price_offer : $appPlan->price;

    $GET        = request()->query('coupon');
    $couponCode = 'bhw';
    if ($GET == $couponCode) {
      $price = 3.99;
    }

    // Check if user is authenticated
    $isAuthenticated = Auth::check();

    $freeApp = 0;
    if ($isAuthenticated) {
      $user = Auth::user();
      $uid  = $user->id;
      $freeApp = DB::table('app_free')
        ->where('uid', $uid)
        ->first();
      $freeApp = $freeApp ? 1 : 0;
    }

    return view('buy-app', compact(
      'price',
      'appPlan',
      'freeApp',
      'isAuthenticated'
    ));
  }

  public function pricing()
  {
    return $this->index();
  }
}
