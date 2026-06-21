<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ServerTimeWidget extends Widget
{
  protected string $view = 'filament.widgets.server-time-widget';

  protected int|string|array $columnSpan = [
    'default' => 1,
    'md' => 2,
  ];
  // protected int|string|array $columnSpan = 1; // هنوزعها من خارج بـ getColumns()

  protected ?string $pollingInterval = '60s';
}
