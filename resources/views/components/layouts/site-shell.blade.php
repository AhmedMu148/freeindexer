<div class="min-h-screen flex flex-col">
  {{-- Header بتاع موقعك --}}
  ييي
  {{-- @include('partials.site-header') --}}

  {{-- Content --}}
  <main class="flex-1 flex items-center justify-center p-6">
    <div class="w-full max-w-md">
      {{ $slot }}
    </div>
  </main>

  {{-- Footer بتاع موقعك --}}
  {{-- @include('partials.site-footer') --}}
</div>