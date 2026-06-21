@component('mail::message')
# New Feedback Received

**Name:** {{ $data['name'] }}
**Email:** {{ $data['email'] }}

---

{{ $data['message'] }}

---

Thanks,<br>
{{ config('app.name') }}
@endcomponent