<x-filament-panels::page>
  <div
    style="min-height: calc(100vh - 120px); display:flex; justify-content:center; align-items:flex-start; padding-top:40px;">
    <div style="width: 100%; max-width: 720px;">
      <x-filament::section>
        <form wire:submit.prevent="save" style="display:flex; flex-direction:column; gap:16px;">
          {{ $this->form }}

          <div style="display:flex; gap:12px;">
            <x-filament::button type="submit">
              Update Profile
            </x-filament::button>

            <x-filament::button type="button" color="gray" outlined wire:click="dashboard">
              Cancel
            </x-filament::button>
          </div>
        </form>
      </x-filament::section>
    </div>
  </div>
</x-filament-panels::page>