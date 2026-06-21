@php
    $heading = $data['heading'] ?? '';
    $subheading = $data['subheading'] ?? '';
    $items = $data['items'] ?? [];
@endphp
<section class="py-5 bg-light">
    <div class="container">
        @if(filled($heading) || filled($subheading))
            <div class="text-center mb-5">
                @if(filled($heading))
                    <h2 class="title">{{ $heading }}</h2>
                @endif
                @if(filled($subheading))
                    <p class="description text-muted">{{ $subheading }}</p>
                @endif
            </div>
        @endif
        <div class="row">
            @foreach($items as $item)
                @php
                    $quote = $item['quote'] ?? '';
                    $author = $item['author'] ?? '';
                    $role = $item['role'] ?? '';
                    $company = $item['company'] ?? '';
                    $rating = intval($item['rating'] ?? 5);
                @endphp
                <div class="col-md-4 mb-4">
                    <div class="card p-4 h-100 shadow-sm border-0">
                        <div class="stars mb-3 text-warning">
                            @for($i = 0; $i < 5; $i++)
                                <i class="{{ $i < $rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="card-text italic mb-4">"{{ $quote }}"</p>
                        <div class="author">
                            <h6 class="font-weight-bold mb-0 text-dark">{{ $author }}</h6>
                            @if(filled($role) || filled($company))
                                <small class="text-muted">
                                    {{ $role }}@if(filled($role) && filled($company)), @endif{{ $company }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
