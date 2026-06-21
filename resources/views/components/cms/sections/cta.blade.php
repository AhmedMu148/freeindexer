@php
    $heading = $data['heading'] ?? '';
    $body = $data['body'] ?? '';
    $buttonLabel = $data['button_label'] ?? '';
    $buttonUrl = $data['button_url'] ?? '';
@endphp
<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(filled($heading))
                    <h2 class="mb-3 text-white font-weight-bold">{{ $heading }}</h2>
                @endif
                @if(filled($body))
                    <p class="lead text-white-50 mb-4">{{ $body }}</p>
                @endif
                @if(filled($buttonLabel) && filled($buttonUrl))
                    <a href="{{ $buttonUrl }}" class="btn btn-warning btn-round btn-lg">{{ $buttonLabel }}</a>
                @endif
            </div>
        </div>
    </div>
</section>
