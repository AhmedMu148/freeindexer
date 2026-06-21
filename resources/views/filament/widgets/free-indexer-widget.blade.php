<x-filament-widgets::widget>
  <x-filament::section class="fi-app-card">
    <x-slot name="heading">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span class="text-base font-semibold">Free Indexer App</span>
          <span class="fi-badge">10× faster</span>
        </div>

      </div>
    </x-slot>

    <div class="flex flex-col items-center gap-4 text-center">
      {{-- Image --}}
      <div class="fi-image-wrap mx-auto">
        <img src="{{ asset('assets/images/freeindexer_app.png') }}" alt="Indexer preview"
          class="fi-image mx-auto block" />
      </div>

      {{-- Text --}}
      {{-- <div class="space-y-1">
        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
          Link Indexer
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-300">
          Index your links faster with a clean, lightweight app experience.
        </div>
      </div> --}}

      {{-- CTA --}}
      <div class="w-full">
        <x-filament::button tag="a" href="{{ route('buy-app') }}" icon="heroicon-m-arrow-down-tray" size="lg"
          color="white" class="fi-cta">
          Download Now
        </x-filament::button>

        {{-- <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
          After purchase, you’ll get instant access to the download.
        </div> --}}
      </div>
    </div>

    <style>
      /* Card polish */
      .fi-app-card {
        overflow: hidden;
      }

      /* Badge */
      .fi-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .2px;
        color: #fff;
        background: rgba(16, 185, 129, .9);
        /* قريب من success */
        box-shadow: 0 6px 18px rgba(16, 185, 129, .25);
        white-space: nowrap;
      }

      /* Image container */
      .fi-image-wrap {
        margin-left: auto;
        margin-right: auto;
        display: flex;
        margin-bottom: 20px;
        justify-content: center;
      }

      .fi-image {
        display: block;
        margin-left: auto;
        margin-right: auto;
      }

      .fi-image-wrap:hover .fi-image {
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(0, 0, 0, .18);
      }

      /* CTA button */
      .fi-cta {
        color: #ffffff;
        width: 100%;
        border-radius: 9999px !important;
        justify-content: center;
        background: #f96332;
      }
    </style>
  </x-filament::section>
</x-filament-widgets::widget>

{{-- <x-filament-widgets::widget>
  <x-filament::section>
    <x-slot name="heading">Free Indexer App</x-slot>
    <div class="flex flex-col items-center gap-4 text-center">
      <img src="{{ asset('assets/images/freeindexer_app.png') }}" alt="Indexer preview"
        class="rounded-md shadow w-48 h-auto object-cover" />

      <div class="text-gray-700">Link Indexer (10X faster)</div>

      <x-filament::button tag="a" href="{{ route('buy-app') }}" icon="heroicon-m-arrow-down-tray" color="success"
        size="lg" class="rounded-full">
        Download Now
      </x-filament::button>
    </div>
  </x-filament::section>
</x-filament-widgets::widget> --}}