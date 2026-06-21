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
        <h2 style="margin:0 0 12px;color:#222;font-size:20px">Thanks for your order 🎉</h2>
        <p style="margin:0 0 18px;color:#555;font-size:14px;line-height:1.7">Order ID:
          <strong>#{{ $orderId }}</strong><br>Plan: {{ $planName }}<br>Date: {{ $orderDate }}
        </p>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:12px 0">
          <tr>
            <td style="padding:8px;border-bottom:1px solid #eee;color:#555">Subtotal</td>
            <td align="right" style="padding:8px;border-bottom:1px solid #eee;color:#222">
              <strong>{{ $subtotal }}</strong>
            </td>
          </tr>
          <tr>
            <td style="padding:8px;border-bottom:1px solid #eee;color:#555">Tax</td>
            <td align="right" style="padding:8px;border-bottom:1px solid #eee;color:#222">
              <strong>{{ $tax }}</strong>
            </td>
          </tr>
          <tr>
            <td style="padding:8px;color:#555">Total</td>
            <td align="right" style="padding:8px;color:#222"><strong>{{ $total }}</strong></td>
          </tr>
        </table>
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
      <td style="padding:18px 24px;background:#f8f9fa;color:#999;font-size:12px;text-align:center">Need help? <a
          href="{{ $supportUrl }}" style="color:#f96332">Contact support</a></td>
    </tr>
  </table>
@endsection