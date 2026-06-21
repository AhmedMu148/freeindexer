<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FreeIndexerWidget extends Widget
{
  protected int|string|array $columnSpan = [
    'default' => 1,
    'md'      => 1,
  ];

  protected string $view = 'filament.widgets.free-indexer-widget';
  // protected int|string|array $columnSpan = 1;
}
