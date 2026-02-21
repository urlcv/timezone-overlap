<?php

declare(strict_types=1);

namespace URLCV\TimezoneOverlap\Laravel;

use App\Tools\Contracts\ToolInterface;

class TimezoneOverlapTool implements ToolInterface
{
    public function slug(): string
    {
        return 'timezone-overlap';
    }

    public function name(): string
    {
        return 'Time Zone Overlap';
    }

    public function summary(): string
    {
        return 'Pick two cities and instantly see which working hours overlap — fully DST-aware.';
    }

    public function descriptionMd(): ?string
    {
        return null;
    }

    public function categories(): array
    {
        return ['time'];
    }

    public function tags(): array
    {
        return ['timezone', 'remote', 'scheduling', 'global'];
    }

    public function inputSchema(): array
    {
        return [];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function frontendView(): ?string
    {
        return 'timezone-overlap::timezone-overlap';
    }

    public function rateLimitPerMinute(): int
    {
        return 60;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 90;
    }
}
