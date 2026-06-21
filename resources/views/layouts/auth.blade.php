{{-- <x-filament::layouts.base> --}}
  <div class="min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="border-b p-4 text-center">
      {{-- <x-filament::brand /> --}}
    </header>

    {{-- Content --}}
    <main class="flex-1 flex items-center justify-center">
      {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t p-4 text-center text-sm text-gray-500">
      © {{ date('Y') }} Your Company
    </footer>

  </div>
{{-- </x-filament::layouts.base> --}}