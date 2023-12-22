<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Tools;

use Ddrv\Router\Contract\ParsedPatternInterface;
use Ddrv\Router\Contract\PatternParserInterface;

final class TestPatternParser implements PatternParserInterface
{
    private array $parseCalls = [];
    private array $getParameterNamesCalls = [];
    private array $wrapParameterCalls = [];
    private PatternParserInterface $patternParser;

    public function __construct(PatternParserInterface $patternParser)
    {
        $this->patternParser = $patternParser;
    }

    public function parse(string $pattern, array $parameterRegexps): ParsedPatternInterface
    {
        $key = $this->getParseKey($pattern, $parameterRegexps);
        if (!array_key_exists($key, $this->parseCalls)) {
            $this->parseCalls[$key] = 0;
        }
        $this->parseCalls[$key]++;

        return $this->patternParser->parse($pattern, $parameterRegexps);
    }

    public function getParameterNames(string $pattern): array
    {
        $key = $this->getGetParameterNamesKey($pattern);
        if (!array_key_exists($key, $this->getParameterNamesCalls)) {
            $this->getParameterNamesCalls[$key] = 0;
        }
        $this->getParameterNamesCalls[$key]++;

        return $this->patternParser->getParameterNames($pattern);
    }

    public function wrapParameter(string $parameter): string
    {
        $key = $this->getWrapParameterKey($parameter);
        if (!array_key_exists($key, $this->wrapParameterCalls)) {
            $this->wrapParameterCalls[$key] = 0;
        }
        $this->wrapParameterCalls[$key]++;

        return $this->patternParser->wrapParameter($parameter);
    }

    public function countParseCalls(string $pattern, array $parameterRegexps): int
    {
        $key = $this->getParseKey($pattern, $parameterRegexps);
        return $this->parseCalls[$key] ?? 0;
    }

    public function countGetParameterNamesCalls(string $pattern): int
    {
        $key = $this->getGetParameterNamesKey($pattern);
        return $this->getParameterNamesCalls[$key] ?? 0;
    }

    public function countWrapParameterCalls(string $parameter): int
    {
        $key = $this->getWrapParameterKey($parameter);
        return $this->wrapParameterCalls[$key] ?? 0;
    }

    private function getParseKey(string $pattern, array $parameterRegexps): string
    {
        $key = $pattern;
        ksort($parameterRegexps);
        foreach ($parameterRegexps as $parameter => $regexp) {
            $key = sprintf('%s_(%s=>%s)', $key, $parameter, $regexp);
        }
        return $key;
    }

    private function getGetParameterNamesKey(string $pattern): string
    {
        return $pattern;
    }

    private function getWrapParameterKey(string $parameter): string
    {
        return $parameter;
    }
}
