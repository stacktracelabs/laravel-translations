<?php


namespace StackTrace\Translations;


/**
 * @implements \StackTrace\Translations\LocalizedValue<string>
 */
class LocalizedString extends LocalizedValue
{
    /**
     * Get the string value.
     */
    public function toString(): string
    {
        return (string) $this;
    }

    /**
     * Get the string value.
     */
    public function __toString(): string
    {
        return $this->getValue() || '';
    }
}
