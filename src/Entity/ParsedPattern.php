<?php

declare(strict_types=1);

namespace Ddrv\Router\Entity;

use Ddrv\Router\Contract\ParsedPatternInterface;

final class ParsedPattern implements ParsedPatternInterface
{
    /**
     * @var non-empty-string
     */
    private string $regexp;
    /**
     * @var string[]
     */
    private array $parameterNames;

    /**
     * @param non-empty-string $regexp
     * @param string[] $parameterNames
     */
    public function __construct(string $regexp, array $parameterNames)
    {
        $this->regexp = $regexp;
        $this->parameterNames = $parameterNames;
    }

    /**
     * @inheritDoc
     */
    public function getRegexp(): string
    {
        return $this->regexp;
    }

    /**
     * @inheritDoc
     */
    public function getParameterNames(): array
    {
        return $this->parameterNames;
    }
}
