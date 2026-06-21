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
        <h2 style="margin:0 0 12px;color:#222;font-size:20px">Order update</h2>
        <p style="margin:0 0 18px;color:#555;font-size:14px;line-height:1.7">Order
          <strong>#{{ $orderId }}</strong> is now <strong>{{ $statusText }}</strong>.<br>Updated on:
          {{ $updatedAt }}
        </p>
        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0">
          <tr>
            <td bgcolor="#2f2f2f" style="border-radius:6px">
              <a href="{{ $orderUrl }}"
                style="display:inline-block;padding:12px 22px;color:#fff;text-decoration:none;font-weight:bold;font-size:14px">View
                Order</a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="padding:18px 24px;background:#f8f9fa;color:#999;font-size:12px;text-align:center">If you have any
        questions, reply to this email.</td>
    </tr>
  </table>

@endsection