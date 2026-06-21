@php
    $heading = $data['heading'] ?? '';
    $items = $data['items'] ?? [];
@endphp
<section class="py-5 bg-light">
    <div class="container">
        @if(filled($heading))
            <h2 class="title text-center mb-5">{{ $heading }}</h2>
        @endif
        <div class="row text-center justify-content-center">
            @foreach($items as $item)
                @php
                    $value = $item['value'] ?? '';
                    $label = $item['label'] ?? '';
                @endphp
                <div class="col-md-3 col-sm-6 mb-4">
                    <h3 class="display-4 font-weight-bold text-warning mb-1">{{ $value }}</h3>
                    <p class="text-muted text-uppercase small font-weight-bold">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
