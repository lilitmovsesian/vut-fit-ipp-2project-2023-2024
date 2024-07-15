<?php

namespace IPP\Student;


/**
 * Represents a data structure to hold variable type and its value.
 */
class VariableTypeData 
{
    public string $type;
    public string $value;

    /**
     * VariableTypeData constructor.
     * @param string $type The type of the variable.
     * @param string $value The value of the variable.
     */
    public function __construct(string $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }
    
    /**
     * Creates an instance of VariableTypeData with empty values.
     */
    public static function createEmpty() : VariableTypeData{
        return new VariableTypeData('', '');
    }
}