<?php

namespace IPP\Student;

/**
 * Class Argument
 * Represents a XML argument with a type and a value specified in the dom.
 */
class Argument 
{
    public string $type;
    public string $value;

    /**
     * Argument constructor.
     * @param string $type The type of the argument.
     * @param string $value The value of the argument.
     */
    public function __construct(string $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }
}