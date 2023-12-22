<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\ParsedPatternInterface;
use Ddrv\Router\Contract\PatternParserInterface;

final class PatternParserCacheDecorator implements PatternParserInterface
{
    private PatternParserInterface $patternParser;
    /** @var array<string, ParsedPatternInterface> */
    private array $cacheParsed = [];
    /** @var array<string, string[]> */
    private array $cacheParameterNames = [];
    /** @var array<string, string> */
    private array $cacheWrappedParameters = [];

    public function __construct(PatternParserInterface $patternParser)
    {
        $this->patternParser = $patternParser;
    }

    /**
     * @inheritDoc
     */
    public function parse(string $pattern, array $parameterRegexps): ParsedPatternInterface
    {
        $key = $pattern;
        foreach ($parameterRegexps as $parameter => $regexp) {
            $key = sprintf('%s_(%s=>%s)', $key, $parameter, $regexp);
        }

        if (!array_key_exists($key, $this->cacheParsed)) {
            $this->cacheParsed[$key] = $this->patternParser->parse($pattern, $parameterRegexps);
        }
        return $this->cacheParsed[$key];
    }

    /**
     * @inheritDoc
     */
    public function getParameterNames(string $pattern): array
    {
        $key = $pattern;

        if (!array_key_exists($key, $this->cacheParameterNames)) {
            $this->cacheParameterNames[$key] = $this->patternParser->getParameterNames($pattern);
        }
        return $this->cacheParameterNames[$key];
    }

    /**
     * @inheritDoc
     */
    public function wrapParameter(string $parameter): string
    {
        $key = $parameter;

        if (!array_key_exists($key, $this->cacheWrappedParameters)) {
            $this->cacheWrappedParameters[$key] = $this->patternParser->wrapParameter($parameter);
        }
        return $this->cacheWrappedParameters[$key];
    }
}
