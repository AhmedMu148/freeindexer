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
                    $name = $item['name'] ?? '';
                    $role = $item['role'] ?? '';
                    $photoUrl = $item['photo_url'] ?? '';
                    $bio = $item['bio'] ?? '';
                @endphp
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card p-3 h-100 shadow-sm border-0 text-center">
                        @if(filled($photoUrl))
                            <img src="{{ $photoUrl }}" alt="{{ $name }}" class="img-fluid rounded-circle mb-3 mx-auto" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle mb-3 mx-auto bg-warning text-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 2rem;">
                                {{ strtoupper(substr($name, 0, 1)) }}
                            </div>
                        @endif
                        <h5 class="font-weight-bold text-dark mb-1">{{ $name }}</h5>
                        <p class="text-warning small mb-3">{{ $role }}</p>
                        <p class="card-text text-muted small">{{ $bio }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
