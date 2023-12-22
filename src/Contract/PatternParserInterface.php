<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface PatternParserInterface
{
    /**
     * @param string $pattern
     * @param array<string, string> $parameterRegexps
     * @return ParsedPatternInterface
     */
    public function parse(string $pattern, array $parameterRegexps): ParsedPatternInterface;

    /**
     * @param string $pattern
     * @return string[]
     */
    public function getParameterNames(string $pattern): array;

    /**
     * @param string $parameter
     * @return string
     */
    public function wrapParameter(string $parameter): string;
}
