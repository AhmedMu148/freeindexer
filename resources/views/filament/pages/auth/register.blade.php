<x-filament-panels::page.simple>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-6">
      {{ $this->form }}
      <x-filament::button style="width:100%; margin-top: 25px" wire:click="register">
        Sign up
      </x-filament::button>
    </div>
  </div>
</x-filament-panels::page.simple>