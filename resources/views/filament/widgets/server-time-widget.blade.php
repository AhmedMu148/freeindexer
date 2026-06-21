<x-filament-widgets::widget>
  <x-filament::section class="fi-time-card">
    <x-slot name="heading">
      <div class="flex items-center justify-between">
        <span class="text-base font-semibold">Server time</span>
        <span class="fi-pill">UTC</span>
      </div>
    </x-slot>

    <div x-data="{
                // ISO أفضل من 'Y-m-d H:i:s' عشان التوافق
                now: new Date('{{ now()->utc()->toIso8601String() }}'),
                resetAt: new Date('{{ now()->utc()->endOfDay()->toIso8601String() }}'),

                tick() { this.now = new Date(this.now.getTime() + 1000) },

                pad(n) { return String(n).padStart(2, '0') },

                timeStr() {
                    return `${this.pad(this.now.getUTCHours())}:${this.pad(this.now.getUTCMinutes())}:${this.pad(this.now.getUTCSeconds())}`
                },

                dateStr() {
                    // عرض بسيط: Feb 09, 2026
                    const m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
                    return `${m[this.now.getUTCMonth()]} ${this.pad(this.now.getUTCDate())}, ${this.now.getUTCFullYear()}`
                },

                remainingParts() {
                    const diff = Math.max(0, this.resetAt - this.now);
                    const total = Math.floor(diff / 1000);
                    const h = Math.floor(total / 3600);
                    const m = Math.floor((total % 3600) / 60);
                    const s = total % 60;
                    return { h, m, s, total };
                },

                remainingText() {
                    const { h, m, s } = this.remainingParts();
                    return `${h}h ${m}m ${s}s`;
                },

                progressPct() {
                    // نسبة الوقت المتبقي من اليوم (0 -> 100)
                    const { total } = this.remainingParts();
                    const day = 24 * 3600;
                    return Math.max(0, Math.min(100, Math.round((total / day) * 100)));
                },
            }" x-init="setInterval(() => tick(), 1000)" class="space-y-4">
      {{-- Big time --}}
      <div class="text-center">
        <div class="fi-time" x-text="timeStr()"></div>
        <div class="fi-sub" x-text="dateStr()"></div>
      </div>

      {{-- Countdown --}}
      <div class="fi-countdown">
        <div class="fi-countdown__row">
          <span class="fi-countdown__label">Points reset in</span>
          <span class="fi-countdown__value" x-text="remainingText()"></span>
        </div>

        <div class="fi-bar">
          <div class="fi-bar__fill" :style="`width: ${progressPct()}%`"></div>
        </div>

        <div class="fi-hint">
          Resets at <span class="font-semibold">23:59:59 UTC</span>
        </div>
      </div>
    </div>

    <style>
      .fi-time-card {
        overflow: hidden;
      }


      .fi-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        /* padding: 6px 10px; */
        padding: 2.9px 10px;
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

      /* .fi-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(0, 0, 0, .06);
        color: rgba(17, 24, 39, .9);
      } */

      .dark .fi-pill {
        background: rgba(255, 255, 255, .08);
        color: rgba(255, 255, 255, .9);
      }

      .fi-time {
        font-size: 40px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: .5px;
        color: rgba(17, 24, 39, .95);
      }

      .dark .fi-time {
        color: rgba(255, 255, 255, .95);
      }

      .fi-sub {
        margin-top: 8px;
        font-size: 13px;
        color: rgba(75, 85, 99, .95);
      }

      .dark .fi-sub {
        color: rgba(209, 213, 219, .9);
      }

      .fi-countdown {
        border-radius: 16px;
        padding: 14px 14px;
        background: rgba(0, 0, 0, .03);
      }

      .dark .fi-countdown {
        background: rgba(255, 255, 255, .06);
      }

      .fi-countdown__row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 10px;
      }

      .fi-countdown__label {
        font-size: 13px;
        color: rgba(75, 85, 99, .95);
      }

      .dark .fi-countdown__label {
        color: rgba(209, 213, 219, .9);
      }

      .fi-countdown__value {
        font-size: 14px;
        font-weight: 800;
        color: rgba(17, 24, 39, .95);
        white-space: nowrap;
      }

      .dark .fi-countdown__value {
        color: rgba(255, 255, 255, .95);
      }

      .fi-bar {
        height: 10px;
        border-radius: 9999px;
        background: rgba(0, 0, 0, .08);
        overflow: hidden;
      }

      .dark .fi-bar {
        background: rgba(255, 255, 255, .12);
      }

      .fi-bar__fill {
        height: 100%;
        border-radius: 9999px;
        background: rgba(16, 185, 129, .9);
        /* success-ish */
        box-shadow: 0 10px 22px rgba(16, 185, 129, .18);
        transition: width .35s ease;
      }

      .fi-hint {
        margin-top: 10px;
        font-size: 12px;
        color: rgba(107, 114, 128, .95);
      }

      .dark .fi-hint {
        color: rgba(209, 213, 219, .75);
      }
    </style>
  </x-filament::section>
</x-filament-widgets::widget>


{{-- <x-filament-widgets::widget>
  <x-filament::section>
    <x-slot name="heading">Server time</x-slot>

    <div x-data="{
            now: new Date('{{ now()->format('Y-m-d H:i:s') }}'),
            resetAt: new Date('{{ now()->endOfDay()->format('Y-m-d 23:59:59') }}'),
            tick() {
                this.now = new Date(this.now.getTime() + 1000);
            },
            remaining() {
                const diff = Math.max(0, this.resetAt - this.now);
                const h = Math.floor(diff / 1000 / 3600);
                const m = Math.floor(diff / 1000 / 60) % 60;
                const s = Math.floor(diff / 1000) % 60;
                return `${h}h ${m}m ${s}s`;
            }
        }" x-init="setInterval(() => tick(), 1000)" class="text-center space-y-2">
      <div class="text-sm text-gray-600">
        {{ now()->format('M d, Y') }} - <span x-text="now.toTimeString().substring(0, 8)"></span>
      </div>

      <div class="text-gray-700">
        Your points will reset in
        <span class="font-semibold" x-text="remaining()"></span>
      </div>
    </div>
  </x-filament::section>
</x-filament-widgets::widget> --}}