@php
    $heading = $data['heading'] ?? '';
    $description = $data['description'] ?? '';
    $items = $data['items'] ?? [];
@endphp
<section class="py-5">
    <div class="container">
        @if(filled($heading) || filled($description))
            <div class="text-center mb-5">
                @if(filled($heading))
                    <h2 class="title">{{ $heading }}</h2>
                @endif
                @if(filled($description))
                    <p class="description text-muted">{{ $description }}</p>
                @endif
            </div>
        @endif
        <div class="row">
            @foreach($items as $item)
                @php
                    $itemTitle = $item['title'] ?? '';
                    $itemIcon = $item['icon'] ?? 'fas fa-check';
                    $itemDesc = $item['description'] ?? '';
                @endphp
                <div class="col-md-4 mb-4">
                    <div class="card p-4 h-100 shadow-sm border-0 text-center">
                        <div class="icon text-warning mb-3">
                            <i class="{{ $itemIcon }} fa-3x"></i>
                        </div>
                        <h4 class="card-title font-weight-bold">{{ $itemTitle }}</h4>
                        <p class="card-text text-muted">{{ $itemDesc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
