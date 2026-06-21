@php
    $heading = $data['heading'] ?? '';
    $items = $data['items'] ?? [];
@endphp
<section class="py-5">
    <div class="container">
        @if(filled($heading))
            <h2 class="title text-center mb-5">{{ $heading }}</h2>
        @endif
        <div class="row">
            @foreach($items as $item)
                @php
                    $imageUrl = $item['image_url'] ?? '';
                    $caption = $item['caption'] ?? '';
                @endphp
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm overflow-hidden h-100">
                        <img src="{{ $imageUrl }}" alt="{{ $caption }}" class="img-fluid" style="width: 100%; height: 250px; object-fit: cover;">
                        @if(filled($caption))
                            <div class="card-body p-3 bg-white text-center">
                                <p class="card-text text-muted small">{{ $caption }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
