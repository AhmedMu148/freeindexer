<?php

// namespace App\Http\Responses;

// use Filament\Facades\Filament;
// use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

// class LoginResponse implements LoginResponseContract
// {
//   public function toResponse($request)
//   {
//     // ناخد redirect من السيشن ونمسحه عشان ما يتكرر
//     $redirect = session()->pull('after_login_redirect');

//     // لو فيه redirect محفوظ، وتأكدنا إنه جوه نفس الدومين
//     if ($redirect && str_starts_with($redirect, url('/'))) {
//       return redirect()->to($redirect);
//     }

//     // لو مفيش → نرجع للسلوك الطبيعي بتاع Filament (panel URL)
//     return redirect()->intended(Filament::getUrl());
//   }
// }
