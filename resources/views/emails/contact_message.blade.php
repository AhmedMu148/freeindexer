<x-mail::message>
  # Introduction

  The body of your message.

  <x-mail::button :url="''">
    Button Text
  </x-mail::button>

  Thanks,<br>
  {{ config('app.name') }}
  <p><strong>Name:</strong> {{ $data['name'] }}</p>
  <p><strong>Email:</strong> {{ $data['email'] }}</p>
  <p><strong>Message:</strong></p>
  <p style="white-space:pre-wrap">{{ $data['message'] }}</p>
</x-mail::message>