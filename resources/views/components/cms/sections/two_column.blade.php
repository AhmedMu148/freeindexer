@php
    $heading = $data['heading'] ?? '';
    $left = $data['left'] ?? '';
    $right = $data['right'] ?? '';
@endphp
<section class="py-5">
    <div class="container">
        @if(filled($heading))
            <h2 class="title text-center mb-5">{{ $heading }}</h2>
        @endif
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="two-column-content">
                    {!! str_starts_with(trim($left), '<') ? $left : \Illuminate\Support\Str::markdown($left) !!}
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="two-column-content">
                    {!! str_starts_with(trim($right), '<') ? $right : \Illuminate\Support\Str::markdown($right) !!}
                </div>
            </div>
        </div>
    </div>
</section>
