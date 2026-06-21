<!doctype html>
<html lang="ar">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @filamentStyles
</head>

<body class="min-h-screen">
  <div class="min-h-screen flex flex-col">

    <header class="border-b p-4 text-center">
      @php($panel = filament()->getCurrentPanel())

      <div class="text-xl font-bold">
        {{ $panel?->getBrandName() ?? config('app.name') }}
      </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-6">
      {{ $slot }}
    </main>

    <footer class="border-t p-4 text-center text-sm text-gray-500">
      © {{ date('Y') }} {{ $panel?->getBrandName() ?? config('app.name') }}
    </footer>
  </div>

  @filamentScripts
</body>

</html>