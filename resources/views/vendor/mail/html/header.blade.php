@props(['url'])
<tr>
  <td class="header">
    <a href="{{ config('app.url') }}" style="display:inline-block;">
      <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Free Indexer" height="40">
    </a>

    {{-- <a href="{{ $url }}" style="display: inline-block;">
      @if (trim($slot) === 'Laravel')
        <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
      @else
        {!! $slot !!}
      @endif
    </a> --}}

  </td>
</tr>