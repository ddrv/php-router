<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\ParsedPatternInterface;
use Ddrv\Router\Contract\PatternParserInterface;
use Ddrv\Router\Entity\ParsedPattern;

final class PatternParser implements PatternParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(string $pattern, array $parameterRegexps): ParsedPatternInterface
    {
        $pairs = [
            '.' => '\\.',
        ];

        $parameterNames = $this->getParameterNames($pattern);
        foreach ($parameterNames as $parameterName) {
            $parameterRegexp = $parameterRegexps[$parameterName] ?? '[^\/]*';
            $pairs[sprintf('{%s}', $parameterName)] = sprintf('(?<%s>%s)', $parameterName, $parameterRegexp);
        }
        $regexp = strtr($pattern, $pairs);
        return new ParsedPattern(sprintf('#^%s$#u', $regexp), $parameterNames);
    }

    /**
     * @inheritDoc
     */
    public function getParameterNames(string $pattern): array
    {
        $parameterNames = [];
        $len = strlen($pattern);
        $offset = 0;

        do {
            $begin = strpos($pattern, '{', $offset);
            if ($begin === false) {
                return $parameterNames;
            }
            $offset = $begin + 1;
            $end = strpos($pattern, '}', $offset);
            if ($end === false) {
                return $parameterNames;
            }

            $parameterNames[] = substr($pattern, $begin + 1, $end - $begin - 1);
            $offset = $end + 1;
        } while ($offset < $len);
        return $parameterNames;
    }

    /**
     * @inheritDoc
     */
    public function wrapParameter(string $parameter): string
    {
        return sprintf('{%s}', $parameter);
    }
}
