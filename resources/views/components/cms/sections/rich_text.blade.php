@php
    $heading = $data['heading'] ?? '';
    $body = $data['body'] ?? '';
@endphp
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(filled($heading))
                    <h2 class="title text-center mb-4">{{ $heading }}</h2>
                @endif
                @if(filled($body))
                    <div class="rich-text-content">
                        {!! \Illuminate\Support\Str::markdown($body) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
