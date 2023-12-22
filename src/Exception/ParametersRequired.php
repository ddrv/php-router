<?php

declare(strict_types=1);

namespace Ddrv\Router\Exception;

use InvalidArgumentException;
use Throwable;

class ParametersRequired extends InvalidArgumentException
{
    /**
     * @var non-empty-array<string>
     */
    private array $requiredParameters;

    /**
     * @param non-empty-array<string> $requiredParameters
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $requiredParameters,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->requiredParameters = $requiredParameters;
    }

    /**
     * @return non-empty-array<string>
     */
    public function getRequiredParameters(): array
    {
        return $this->requiredParameters;
    }
}
