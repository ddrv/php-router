<?php

declare(strict_types=1);

namespace Ddrv\Router\Exception;

use RuntimeException;
use Throwable;

class MethodNotAllowed extends RuntimeException
{
    /**
     * @var non-empty-array<string>
     */
    private array $allowedMethods;

    /**
     * @param non-empty-array<string> $allowedMethods
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $allowedMethods,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * @return non-empty-array<string>
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
