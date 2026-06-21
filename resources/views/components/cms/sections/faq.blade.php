@php
    $heading = $data['heading'] ?? '';
    $description = $data['description'] ?? '';
    $items = $data['items'] ?? [];
    $accordionId = 'faq-accordion-' . uniqid();
@endphp
<section class="py-5 bg-light">
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="{{ $accordionId }}">
                    @foreach($items as $index => $item)
                        @php
                            $question = $item['question'] ?? '';
                            $answer = $item['answer'] ?? '';
                            $itemId = $accordionId . '-item-' . $index;
                        @endphp
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header" id="heading-{{ $itemId }}">
                                <button class="accordion-button collapsed font-weight-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $itemId }}" aria-expanded="false" aria-controls="collapse-{{ $itemId }}">
                                    {{ $question }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $itemId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $itemId }}" data-bs-parent="#{{ $accordionId }}">
                                <div class="accordion-body text-muted">
                                    {{ $answer }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
