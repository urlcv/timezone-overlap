<?php

declare(strict_types=1);

namespace URLCV\TimezoneOverlap\Laravel;

use Illuminate\Support\ServiceProvider;

class TimezoneOverlapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'timezone-overlap');
    }
}
