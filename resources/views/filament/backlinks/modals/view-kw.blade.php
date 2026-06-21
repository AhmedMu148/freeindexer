<div class="space-y-2">
  @if (empty($keywords))
    <div class="text-gray-500">No links found in this file.</div>
  @else
    <ul class="list-disc ps-5 space-y-1">
      @foreach ($keywords as $keyword)
        <li>
          <a href="{{ $keyword }}" target="_blank" class="text-primary-600 underline break-all">
            {{ $keyword }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>