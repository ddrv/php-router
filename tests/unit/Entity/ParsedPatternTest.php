<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Entity;

use Ddrv\Router\Entity\ParsedPattern;
use PHPUnit\Framework\TestCase;

final class ParsedPatternTest extends TestCase
{
    /**
     * @param string $regexp
     * @param string[] $parameterNames
     * @return void
     * @dataProvider provideCreate
     */
    public function testCreate(string $regexp, array $parameterNames): void
    {
        $parsedPattern = new ParsedPattern($regexp, $parameterNames);
        $this->assertSame($regexp, $parsedPattern->getRegexp());
        $this->assertSame($parameterNames, $parsedPattern->getParameterNames());
    }

    /**
     * @return iterable<array{0:string, 1:string[]}>
     */
    public static function provideCreate(): iterable
    {
        $params = [
            ['foo'],
            ['foo', 'bar'],
            ['a', 'b', 'c', 'd'],
        ];
        foreach ($params as $parameterNames) {
            yield [self::createRegexp($parameterNames), $parameterNames];
        }
    }

    /**
     * @param string[] $parameterNames
     * @return string
     */
    private static function createRegexp(array $parameterNames): string
    {
        $parts = [];
        foreach ($parameterNames as $parameterName) {
            $parts[] = sprintf('(?<%s>[^\/]+)', $parameterName);
        }
        return sprintf('#^%s$#u', implode('/', $parts));
    }
}
