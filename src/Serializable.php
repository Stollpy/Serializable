<?php

/*
 * This file is part of the Laradev project
 * (c) Darras Florian florian@laradev.ca
 */

use ArrayAccess;

trait Serializable
{
    public function toArray(object|string|null $object = null): array
    {
        $data = [];
        $object = $object ?? $this;
        $ref = new \ReflectionClass($object);

        foreach ($ref->getProperties() as $property) {
            if (
                !in_array($property->getName(), ["whitelist", "blacklist"]) &&
                $this->isInWhitelist($property, $object) &&
                !$this->isInBlacklist($property, $object)
            ) {
                $value = $this->extractValueFromProperty($property, $object);
                $data[$property->getName()] = $this->extractValueFromType($value);
            }
        }

        return $data;
    }

    private function isInWhitelist(\ReflectionProperty $property, object $object): bool
    {
        $ref = new \ReflectionClass($object);
        $whitelist = [];
        $whitelistExist = false;

        if ($ref->hasProperty("whitelist")) {
            $whitelist = $object->whitelist;
            $whitelistExist = true;
        }

        if ($ref->hasMethod("whitelist")) {
            $whitelist = $object->whitelist();
            $whitelistExist = true;
        }

        if (!$whitelistExist) {
            return true;
        }

        return in_array($property->getName(), $whitelist);
    }

    private function isInBlacklist(\ReflectionProperty $property, object $object): bool
    {
        $ref = new \ReflectionClass($object);
        $blacklist = [];
        $blacklistExist = false;

        if ($ref->hasProperty("blacklist")) {
            $blacklist = $object->blacklist;
            $blacklistExist = true;
        }

        if ($ref->hasMethod("blacklist")) {
            $blacklist = $object->blacklist();
            $blacklistExist = true;
        }

        if (!$blacklistExist) {
            return false;
        }

        return in_array($property->getName(), $blacklist);
    }

    private function extractValueFromProperty(\ReflectionProperty $property, object $object): mixed
    {
        if (!$property->isInitialized($object)) {
            return null;
        }

        if ($property->isPublic()) {
            return $property->getValue($object);
        }

        $camelCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property->getName()))));
        $getter = 'get'.ucfirst($camelCaseProperty);

        if ((new \ReflectionClass($object))->hasMethod($getter)) {
            return $object->$getter();
        }

        return null;
    }

    private function extractValueFromType(mixed $attr): mixed
    {
        if (is_array($attr) || $attr instanceof ArrayAccess) {
            $data = [];
            foreach ($attr as $key => $value) {
                $data[$key] = $this->extractValueFromType($value);
            }
            return $data;
        } elseif ($attr instanceof \BackedEnum) {
            return $attr->value;
        } elseif (is_object($attr)) {
            return $this->toArray($attr);
        } else {
            return $attr;
        }
    }

    public function toJson(object|string|null $object = null): string
    {
        return json_encode($this->toArray($object ?? $this));
    }
}
