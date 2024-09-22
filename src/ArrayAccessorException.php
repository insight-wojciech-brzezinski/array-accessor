<?php

declare(strict_types=1);

namespace Insight\Component\ArrayAccessor;

use RuntimeException;

final class ArrayAccessorException extends RuntimeException
{
    public function __construct(string $message, array $path)
    {
        parent::__construct(
            sprintf('%s: [%s]', $message, implode('.', $path)),
        );
    }
}
