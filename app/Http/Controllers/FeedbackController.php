<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;
use App\Mail\FeedbackMessage;

class FeedbackController extends Controller
{
  public function store(Request $request)
  {
    // Spam check (honeypot)
    if ($request->filled('website')) {
      return back()->withErrors(['message' => 'Spam detected'])->withInput();
    }

    $data = $request->validate([
      'name'    => ['required', 'string', 'max:100'],
      'email'   => ['required', 'email', 'max:150'],
      'message' => ['required', 'string', 'max:5000'],
    ]);

    $to = config('mail.feedback_to', config('mail.from.address'));

    // Mail::to($to)->send(new FeedbackMail($data));
    Mail::to($to)->send(new FeedbackMessage($data));

    return back()->with('status', 'Your message has been received successfully. We will get back to you soon.');
  }
}
