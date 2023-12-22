<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Service\PatternParser;
use Ddrv\Router\Service\PatternParserCacheDecorator;
use PHPUnit\Framework\TestCase;
use Tests\Ddrv\Router\Tools\TestPatternParser;

final class PatternParserCacheDecoratorTest extends TestCase
{
    /**
     * @param string $pattern
     * @param array<string, string> $parameterRegexps
     * @return void
     * @dataProvider provideParse
     */
    public function testParse(string $pattern, array $parameterRegexps): void
    {
        $patternParser = $this->createTestPatternParser();
        $decorator = new PatternParserCacheDecorator($patternParser);

        $this->assertSame(0, $patternParser->countParseCalls($pattern, $parameterRegexps));

        $decorator->parse($pattern, $parameterRegexps);
        $this->assertSame(1, $patternParser->countParseCalls($pattern, $parameterRegexps));

        $decorator->parse($pattern, $parameterRegexps);
        $this->assertSame(1, $patternParser->countParseCalls($pattern, $parameterRegexps));

        $decorator->parse($pattern, $parameterRegexps);
        $this->assertSame(1, $patternParser->countParseCalls($pattern, $parameterRegexps));
    }

    /**
     * @param string $pattern
     * @return void
     * @dataProvider provideGetParameterNames
     */
    public function testGetParameterNames(string $pattern): void
    {
        $patternParser = $this->createTestPatternParser();
        $decorator = new PatternParserCacheDecorator($patternParser);

        $this->assertSame(0, $patternParser->countGetParameterNamesCalls($pattern));

        $decorator->getParameterNames($pattern);
        $this->assertSame(1, $patternParser->countGetParameterNamesCalls($pattern));

        $decorator->getParameterNames($pattern);
        $this->assertSame(1, $patternParser->countGetParameterNamesCalls($pattern));

        $decorator->getParameterNames($pattern);
        $this->assertSame(1, $patternParser->countGetParameterNamesCalls($pattern));
    }

    /**
     * @param string $parameter
     * @return void
     * @dataProvider provideWrapParameters
     */
    public function testWrapParameters(string $parameter): void
    {
        $patternParser = $this->createTestPatternParser();
        $decorator = new PatternParserCacheDecorator($patternParser);

        $this->assertSame(0, $patternParser->countWrapParameterCalls($parameter));

        $decorator->wrapParameter($parameter);
        $this->assertSame(1, $patternParser->countWrapParameterCalls($parameter));

        $decorator->wrapParameter($parameter);
        $this->assertSame(1, $patternParser->countWrapParameterCalls($parameter));

        $decorator->wrapParameter($parameter);
        $this->assertSame(1, $patternParser->countWrapParameterCalls($parameter));
    }

    /**
     * @return iterable<array{0:string, 1:array<string, string>}>
     */
    public static function provideParse(): iterable
    {
        return [
            ['/hello/{name}', []],
            ['/hello/{name}', ['name' => '\w+']],
        ];
    }

    /**
     * @return iterable<array{0:string}>
     */
    public static function provideGetParameterNames(): iterable
    {
        return [
            ['/hello/{name}'],
            ['/api/v{version}/users/{login}/posts/{postId}/comments'],
        ];
    }

    /**
     * @return iterable<array{0:string}>
     */
    public static function provideWrapParameters(): iterable
    {
        return [
            ['name'],
            ['version'],
            ['login'],
            ['postId'],
        ];
    }

    private function createTestPatternParser(): TestPatternParser
    {
        $patternParser = new PatternParser();
        return new TestPatternParser($patternParser);
    }
}
