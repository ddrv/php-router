<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Service\PatternParser;
use PHPUnit\Framework\TestCase;

final class PatternParserTest extends TestCase
{
    /**
     * @param string $pattern
     * @param array<string, string> $parameterRegexps
     * @param string $expectedRegexp
     * @param string[] $expectedParameterNames
     * @return void
     * @dataProvider provideParse
     */
    public function testParse(
        string $pattern,
        array $parameterRegexps,
        string $expectedRegexp,
        array $expectedParameterNames
    ): void {
        $patternParser = $this->createPatternParser();

        $parsedPattern = $patternParser->parse($pattern, $parameterRegexps);
        $this->assertSame($expectedRegexp, $parsedPattern->getRegexp());

        $actualParameterNames = $parsedPattern->getParameterNames();
        $this->assertCount(count($expectedParameterNames), $actualParameterNames);
        foreach ($expectedParameterNames as $parameterName) {
            $this->assertTrue(in_array($parameterName, $actualParameterNames, true));
        }
    }

    /**
     * @param string $pattern
     * @param string[] $expectedParameterNames
     * @return void
     * @dataProvider provideGetParameterNames
     */
    public function testGetParameterNames(string $pattern, array $expectedParameterNames): void
    {
        $patternParser = $this->createPatternParser();

        $actualParameterNames = $patternParser->getParameterNames($pattern);

        $this->assertCount(count($expectedParameterNames), $actualParameterNames);
        foreach ($expectedParameterNames as $parameterName) {
            $this->assertTrue(in_array($parameterName, $actualParameterNames, true));
        }
    }

    /**
     * @param string $parameter
     * @param string $expected
     * @return void
     * @dataProvider provideWrapParameters
     */
    public function testWrapParameters(string $parameter, string $expected): void
    {
        $patternParser = $this->createPatternParser();

        $this->assertSame($expected, $patternParser->wrapParameter($parameter));
    }

    /**
     * @return iterable<array{0:string, 1:string, 2:string[]}>
     */
    public static function provideParse(): iterable
    {
        return [
            ['/version', [], '#^/version$#u', []],
            ['/hello/{name}', [], '#^/hello/(?<name>[^\/]*)$#u', ['name']],
            ['/hello/{name}', ['name' => '\w+'], '#^/hello/(?<name>\w+)$#u', ['name']],
        ];
    }

    /**
     * @return iterable<array{0:string, 1:string[]}>
     */
    public static function provideGetParameterNames(): iterable
    {
        return [
            ['version', []],
            ['/hello/{name}', ['name']],
            ['/api/v{version}/users/{login}/posts/{postId}/comments', ['version', 'login', 'postId']],
        ];
    }

    /**
     * @return iterable<array{0:string, 1:string}>
     */
    public static function provideWrapParameters(): iterable
    {
        return [
            ['name', '{name}'],
            ['version', '{version}'],
            ['login', '{login}'],
            ['postId', '{postId}'],
        ];
    }

    private function createPatternParser(): PatternParser
    {
        return new PatternParser();
    }
}
