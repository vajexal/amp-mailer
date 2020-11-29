<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use ReflectionObject;

function setPrivateProperty(object $object, string $property, $value): void
{
    $reflector = new ReflectionObject($object);
    $property = $reflector->getProperty($property);
    $property->setAccessible(true);
    $property->setValue($object, $value);
}
