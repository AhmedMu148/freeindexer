<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
  public function store(Request $request)
  {
    if ($request->filled('website')) {
      return back()->withErrors(['message' => 'Spam detected'])->withInput();
    }

    $data = $request->validate([
      'name'    => ['required', 'string', 'max:100'],
      'email'   => ['required', 'email', 'max:150'],
      'message' => ['required', 'string', 'max:5000'],
    ]);

    $to = config('mail.contact_to', config('mail.from.address'));

    Mail::to($to)->send(new ContactMessage($data));

    return back()->with('status', 'Thank you for contacting us. Your message has been received and we will respond shortly.');
  }
}
