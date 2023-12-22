<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Tools;

final class TestCallback
{
    private int $calls = 0;

    public function __invoke(): void
    {
        $this->calls++;
    }

    public function getCalls(): int
    {
        return $this->calls;
    }
}
