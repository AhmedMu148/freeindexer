@extends('emails.layout')

@section('title', 'FreeIndexer')

@section('content')
  <table width="600" cellpadding="0" cellspacing="0"
    style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)">
    <tr>
      <td style="background:#f96332;padding:18px 24px"><img src="https://freeindexer.com/tpl/img/logo.png"
          alt="FreeIndexer" style="height:36px;display:block"></td>
    </tr>
    <tr>
      <td style="padding:28px 24px">
        <h2 style="margin:0 0 12px;color:#222;font-size:20px">Security alert</h2>
        <p style="margin:0 0 12px;color:#555;font-size:14px;line-height:1.7">Hi {{ $username }}, your account
          email was changed to <strong>{{ $newEmail }}</strong> on {{ $changedAt }}</p>
        <div
          style="margin-top:12px;padding:12px;background:#fff4ef;border:1px solid #ffd6c8;border-radius:8px;color:#7a3f1a;font-size:13px">
          If this wasn’t you, <a href="{{ $secureUrl }}" style="color:#f96332">secure your account</a> and reset
          your password immediately.</div>
      </td>
    </tr>
    <tr>
      <td style="padding:18px 24px;background:#f8f9fa;color:#999;font-size:12px;text-align:center">©
        {{ date('Y') }} FreeIndexer • <a href="{{ $supportUrl }}" style="color:#f96332;text-decoration:none">Support</a>
      </td>
    </tr>
  </table>
@endsection