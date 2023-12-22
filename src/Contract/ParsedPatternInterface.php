<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface ParsedPatternInterface
{
    /**
     * @return non-empty-string
     */
    public function getRegexp(): string;

    /**
     * @return string[]
     */
    public function getParameterNames(): array;
}
