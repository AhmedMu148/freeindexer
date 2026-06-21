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
        <h2 style="margin:0 0 12px;color:#222;font-size:20px">Reset your password</h2>
        <p style="margin:0 0 18px;color:#555;font-size:14px;line-height:1.7">Hi {{ $username }},<br>We received a
          request to reset your password. Click the button below to proceed.</p>
        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0">
          <tr>
            <td bgcolor="#2f2f2f" style="border-radius:6px">
              <a href="{{ $resetUrl }}"
                style="display:inline-block;padding:12px 22px;color:#fff;text-decoration:none;font-weight:bold;font-size:14px">Reset
                Password</a>
            </td>
          </tr>
        </table>
        <p style="margin:16px 0 0;color:#999;font-size:12px">If you didn’t request this, ignore this email. This
          link expires in {{ $expiresIn ?? '60 minutes' }}.</p>
      </td>
    </tr>
    <tr>
      <td style="padding:18px 24px;background:#f8f9fa;color:#999;font-size:12px;text-align:center">©
        {{ date('Y') }} FreeIndexer • <a href="https://freeindexer.com"
          style="color:#f96332;text-decoration:none">freeindexer.com</a>
      </td>
    </tr>
  </table>
@endsection