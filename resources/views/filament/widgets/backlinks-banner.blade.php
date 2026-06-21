<x-filament-widgets::widget>
  <x-filament::section>
    <div class="fi-simple-points">
      <div class="fi-simple-points__top">
        <span class="fi-simple-points__label">Your Remaining Points For SEO Backlinks</span>
        <span class="fi-simple-points__value">{{ $available }}</span>
      </div>

      <div class="fi-simple-points__bottom">
        <span class="fi-simple-points__hint">To access more points</span>

        <a href="{{ route('pricing') }}" target="_blank" rel="noopener" class="fi-upgrade-btn">
          Upgrade Plan
        </a>
      </div>
    </div>

    <style>
      .fi-simple-points {
        width: 100%;
        padding: 4px 0;
      }

      .fi-simple-points__top {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
      }

      .fi-simple-points__label {
        font-size: 14px;
        color: rgba(55, 65, 81, .95);
        /* gray-700 */
      }

      .dark .fi-simple-points__label {
        color: rgba(229, 231, 235, .9);
        /* gray-200 */
      }

      .fi-simple-points__value {
        font-size: 18px;
        font-weight: 800;
        color: rgba(17, 24, 39, .95);
        /* gray-900 */
        white-space: nowrap;
      }

      .dark .fi-simple-points__value {
        color: rgba(255, 255, 255, .95);
      }

      .fi-simple-points__bottom {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
      }

      .fi-simple-points__hint {
        font-size: 13px;
        color: rgba(107, 114, 128, .95);
        /* gray-500 */
      }

      .dark .fi-simple-points__hint {
        color: rgba(209, 213, 219, .85);
      }

      /* Orange button (no shadow) */
      .fi-upgrade-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;

        color: #111827;
        background: rgba(249, 115, 22, .95);
        border: 1px solid rgba(0, 0, 0, .06);

        transition: filter .12s ease, transform .12s ease;
        white-space: nowrap;
      }

      .fi-upgrade-btn:hover {
        filter: brightness(1.05);
        transform: translateY(-1px);
      }

      .fi-upgrade-btn:active {
        filter: brightness(.98);
        transform: translateY(0);
      }
    </style>
  </x-filament::section>
</x-filament-widgets::widget>


{{-- <x-filament-widgets::widget>
  <x-filament::section>
    <div class="w-full mb-4 px-0">
      <div class="w-full bg-blue-600 text-white text-center py-4 rounded shadow">
        <div>
          <strong>Your Remaining Points For SEO Backlinks = </strong> {{ $available }}
        </div>
        <div>
          <strong>To Access More Points </strong>
          <a href="{{ route('pricing') }}" target="_blank" class="underline"><u>Click Here</u></a>
        </div>
      </div>
    </div>
  </x-filament::section>
</x-filament-widgets::widget> --}}