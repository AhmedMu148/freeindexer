<x-filament-panels::page.simple>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-6">
      <form wire:submit.prevent="authenticate">
        {{ $this->form }}
        <x-filament::button type="submit" style="width:100%; margin-top: 25px" wire:loading.attr="disabled"
          wire:target="authenticate" class="w-full mt-6">
          <span x-show="!$wire.__idle" class="inline-block animate-spin mr-2">
            <!-- spinner icon -->
            <svg class="w-5 h-5" viewBox="0 0 24 24">...</svg>
          </span>
          Sign in
        </x-filament::button>
      </form>
    </div>
  </div>
</x-filament-panels::page.simple>