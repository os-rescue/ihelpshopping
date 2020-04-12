<?php

namespace IHelpShopping\DataAccessor;

use IHelpShopping\Exception\MethodNotFoundException;

/**
 * @final
 */
class PropertyAccessor
{
    /**
     * @param object $object
     * @param string $property
     * @return object|string|null
     * @throws MethodNotFoundException
     */
    public function getPropertyValue(object $object, string $property)
    {
        return $object->{$this->getGetterMethod($object, $property)}();
    }

    public function getGetterMethod(object $object, string $property): string
    {
        $ucfirsted = ucfirst($property);

        $getter = 'get'.$ucfirsted;
        if (\is_callable([$object, $getter])) {
            return $getter;
        }

        $isser = 'is'.$ucfirsted;
        if (\is_callable([$object, $isser])) {
            return $isser;
        }

        $hasser = 'has'.$ucfirsted;
        if (\is_callable([$object, $hasser])) {
            return $hasser;
        }

        throw new MethodNotFoundException(sprintf(
            '%s getter method of the class %s not found.',
            $ucfirsted,
            get_class($object)
        ));
    }

    public function getSetterMethodName(object $object, string $property): string
    {
        $setter = sprintf('set%s', ucfirst($property));
        if (\is_callable([$object, $setter])) {
            return $setter;
        }

        throw new MethodNotFoundException(sprintf(
            'Setter method %s of the class %s not found.',
            $setter,
            get_class($object)
        ));
    }
}
