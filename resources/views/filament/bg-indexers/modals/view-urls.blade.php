<div class="space-y-2">
  @if (empty($links))
    <div class="text-gray-500">No links found in this file.</div>
  @else
    <ul class="list-disc ps-5 space-y-1">
      @foreach ($links as $url)
        <li>
          <a href="{{ $url }}" target="_blank" class="text-primary-600 underline break-all">
            {{ $url }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>