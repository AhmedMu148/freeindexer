@php
    $heading = $data['heading'] ?? '';
    $items = $data['items'] ?? [];
@endphp
<section class="py-5 bg-light">
    <div class="container">
        @if(filled($heading))
            <h2 class="title text-center mb-5">{{ $heading }}</h2>
        @endif
        <div class="row justify-content-center align-items-stretch">
            @foreach($items as $item)
                @php
                    $name = $item['name'] ?? '';
                    $price = $item['price'] ?? '0';
                    $period = $item['period'] ?? 'month';
                    $features = $item['features'] ?? [];
                    $btnLabel = $item['button_label'] ?? 'Choose Plan';
                    $btnUrl = $item['button_url'] ?? '#';
                @endphp
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card p-4 h-100 shadow-sm border-0 text-center block block-pricing">
                        <div class="table">
                            <h6 class="category text-warning text-uppercase mb-3">{{ $name }}</h6>
                            <div class="icon mt-2 mb-4">
                                <h3 class="font-weight-bold mb-0">${{ $price }}</h3>
                                <small class="text-muted">/ {{ $period }}</small>
                            </div>
                            <ul class="list-unstyled mb-4">
                                @foreach($features as $feat)
                                    <li class="py-2 text-muted border-bottom small">{{ $feat }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ $btnUrl }}" class="btn btn-warning btn-round w-100 py-3">{{ $btnLabel }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
