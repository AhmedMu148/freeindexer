@php
    $eyebrow = $data['eyebrow'] ?? '';
    $heading = $data['heading'] ?? '';
    $subheading = $data['subheading'] ?? '';
    $buttonLabel = $data['button_label'] ?? '';
    $buttonUrl = $data['button_url'] ?? '';
@endphp
<section class="py-5 py-md-7">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(filled($eyebrow))
                    <h6 class="text-warning text-uppercase font-weight-bold mb-3">{{ $eyebrow }}</h6>
                @endif
                @if(filled($heading))
                    <h1 class="display-3 font-weight-bold mb-4 text-dark">{{ $heading }}</h1>
                @endif
                @if(filled($subheading))
                    <p class="lead text-muted mb-5">{{ $subheading }}</p>
                @endif
                @if(filled($buttonLabel) && filled($buttonUrl))
                    <a href="{{ $buttonUrl }}" class="btn btn-warning btn-round btn-lg px-4 py-3">{{ $buttonLabel }}</a>
                @endif
            </div>
        </div>
    </div>
</section>
