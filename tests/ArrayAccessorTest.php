<?php

declare(strict_types=1);

namespace Tests\Insight\Component\ArrayAccessor;

use Insight\Component\ArrayAccessor\ArrayAccessor;
use Insight\Component\ArrayAccessor\ArrayAccessorException;
use PHPUnit\Framework\TestCase;

final class ArrayAccessorTest extends TestCase
{
    public function testCreatedFromDataWithDefaultName(): void
    {
        // Given
        $data = [uniqid(), uniqid(), uniqid()];

        // When
        $accessor = ArrayAccessor::from($data);

        // Then
        self::assertSame($data, $accessor->root);
        self::assertCount(1, $accessor->path);
        self::assertSame('#', $accessor->path[0]);
    }

    public function testCreatedWithCustomName(): void
    {
        // Given
        $name = 'some-custom-name';
        $nameExpected = '#some-custom-name';

        // When
        $accessor = ArrayAccessor::from([], $name);

        // Then
        self::assertSame($nameExpected, $accessor->path[0]);
    }

    public function test(): void
    {
        // Given
        $accessor = ArrayAccessor::from([]);

        // Expect
        self::expectException(ArrayAccessorException::class);

        // When
        $accessor->string(uniqid());
    }
}
