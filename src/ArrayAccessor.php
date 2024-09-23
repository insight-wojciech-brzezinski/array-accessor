<?php

declare(strict_types=1);

namespace Insight\Component\ArrayAccessor;

use BackedEnum;
use InvalidArgumentException;
use LogicException;

final class ArrayAccessor
{
    /**
     * @var ArrayAccessorConverter<object>[]
     */
    private static array $converters = [];

    private function __construct(public readonly array $root, public readonly array $path)
    {

    }

    public static function from(array $root, string $name = ''): self
    {
        return new self($root, ['#'.$name]);
    }

    /**
     * @param ArrayAccessorConverter<object> ...$converters
     */
    public static function register(ArrayAccessorConverter ...$converters): void
    {
        foreach ($converters as $converter) {
            self::$converters[$converter->type()] = $converter;
        }
    }

    public function array(int|string $key): array
    {
        $value = $this->mixed($key);

        is_array($value) || throw $this->error('Value expected to be an array', $key);

        return $value;
    }

    public function arrayOrNull(int|string $key): ?array
    {
        $value = $this->mixedOrNull($key);

        if (null !== $value) {
            is_array($value) || throw $this->error('Value expected to be an array', $key);
        }

        return $value;
    }

    public function boolean(int|string $key): bool
    {
        $value = $this->mixed($key);

        is_bool($value) || throw $this->error('Value expected to be a boolean', $key);

        return $value;
    }

    public function booleanOrNull(int|string $key): ?bool
    {
        $value = $this->mixedOrNull($key);

        if (null !== $value) {
            is_bool($value) || throw $this->error('Value expected to be a boolean', $key);
        }

        return $value;
    }

//    /**
//     * @template T of object
//     *
//     * @param class-string<T> $into
//     *
//     * @return T
//     */
//    public function convert(string $into): object
//    {
//        /**
//         * @var ArrayAccessorConverter<T> $converter
//         */
//        $converter = self::$converters[$into] ?? throw new InvalidArgumentException();
//
//        return $converter->convert($this);
//    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function enum(int|string $key, string $type): BackedEnum
    {
        is_a($type, BackedEnum::class, true) || throw new LogicException();

        $value = $this->mixed($key);

        if (!is_string($value) && !is_int($value)) {
            throw $this->error('', $key);
        }

        $value = $type::tryFrom($value);

        if (null === $value) {
            throw $this->error('', $key);
        }

        return $value;
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $type
     *
     * @return ?T
     */
    public function enumOrNull(int|string $key, string $type): ?BackedEnum
    {
        is_a($type, BackedEnum::class, true) || throw new LogicException();

        $value = $this->mixedOrNull($key);

        if (null !== $value) {
            if (!is_string($value) && !is_int($value)) {
                throw $this->error('', $key);
            }
            $value = $type::tryFrom($value) ?? throw $this->error('Value expected to be an enum', $key);
        }

        return $value;
    }

    public function detach(string $name = ''): self
    {
        if (count($this->path) === 1) {
            return $this;
        }

        return self::from($this->root, $name);
    }

    public function float(int|string $key): float
    {
        $value = $this->mixed($key);

        is_float($value) || throw $this->error('Value expected to be a float', $key);

        return $value;
    }

    public function floatOrNull(int|string $key): ?float
    {
        $value = $this->mixedOrNull($key);

        if (null !== $value) {
            is_float($value) || throw $this->error('Value expected to be a float', $key);
        }

        return $value;
    }

    public function int(int|string $key): int
    {
        $value = $this->mixed($key);

        is_int($value) || throw $this->error('Value expected to be an integer', $key);

        return $value;
    }

    public function intOrNull(int|string $key): ?int
    {
        $value = $this->mixedOrNull($key);

        if (null !== $value) {
            is_int($value) || throw $this->error('Value expected to be an integer', $key);
        }

        return $value;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function object(int|string $key, string $type)
    {
        $object = $this->objectOrNull($key, $type);

        if (null === $object) {
            throw $this->error('', $key);
        }

        return $object;
//        return $this->move($key)->convert($type);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return ?T
     */
    public function objectOrNull(int|string $key, string $type): ?object
    {
        /**
         * @var ArrayAccessorConverter<T> $converter
         */
        $converter = self::$converters[$type] ?? throw new InvalidArgumentException();

        return $converter->convert($this, $key);
//        return $this->moveOrNull($key)?->convert($type);
    }

    public function mixed(int|string $key): mixed
    {
        $value = $this->mixedOrNull($key);

        is_null($value) && throw $this->error('Value does not exist', $key);

        return $value;
    }

    public function mixedOrNull(int|string $key): mixed
    {
        return $this->root[$key] ?? null;
    }

    public function move(int|string ...$keys): self
    {
        $key = array_shift($keys);

        if (null === $key) {
            return $this;
        }

        $accessor = new self($this->array($key), [...$this->path, $key]);

        return $accessor->move(...$keys);
    }

    public function moveOrNull(int|string ...$keys): ?self
    {
        $key = array_shift($keys);

        if (null === $key) {
            return null;
        }

        $data = $this->arrayOrNull($key);

        if (null === $data) {
            return null;
        }

        $accessor = new self($this->array($key), [...$this->path, $key]);

        return $accessor->moveOrNull(...$keys);
    }

    public function string(int|string $key): string
    {
        $value = $this->mixed($key);

        is_string($value) || throw $this->error('Value expected to be a string', $key);

        return $value;
    }

    public function stringOrNull(int|string $key): ?string
    {
        $value = $this->mixedOrNull($key);

        if (null !== $value) {
            is_string($value) || throw $this->error('Value expected to be a string', $key);
        }

        return $value;
    }

    private function error(string $message, int|string $key): ArrayAccessorException
    {
        return new ArrayAccessorException($message, [...$this->path, $key]);
    }
}
