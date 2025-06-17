<?php

declare(strict_types=1);

namespace Zaxbux\BackblazeB2\Traits;

use RuntimeException;

/**
 * Thanks to thephpleague/flysystem
 * @internal
 */
trait ProxyArrayAccessToPropertiesTrait
{
    private function formatPropertyName(string $offset): string
    {
        return str_replace('_', '', lcfirst(ucwords($offset, '_')));
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        $property = $this->formatPropertyName((string) $offset);

        return isset($this->{$property});
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): mixed
    {
        $property = $this->formatPropertyName((string) $offset);

        return $this->{$property};
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Properties can not be manipulated');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Properties can not be manipulated');
    }
}
