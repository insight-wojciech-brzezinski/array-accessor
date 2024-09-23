<?php

declare(strict_types=1);

namespace Insight\Component\ArrayAccessor;

/**
 * @template T of object
 */
interface ArrayAccessorConverter
{
    /**
     * @return T
     */
    public function convert(ArrayAccessor $accessor, int|string $key): ?object;

    public function type(): string;
}
