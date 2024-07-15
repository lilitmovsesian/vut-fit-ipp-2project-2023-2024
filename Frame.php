<?php

namespace IPP\Student;


/**
 * Class Frame
 * Represents a frame containing variables.
 */
class Frame
{
    /**
    * @var array<string, VariableTypeData> $variableArray
    */
    public array $variableArray;

    /**
     * Frame constructor.
     * Initializes the variable array.
     */

    public function __construct(){
        $this->variableArray = array();
    }

    public function addVariable(string $name): void{
        $this->variableArray[$name] = VariableTypeData::createEmpty();   
    }


    /**
     * Gets the list of variables in the frame.
     * @return array<string, VariableTypeData> An array of variable names and their data.
     */
    public function getVariableList(): array{
        return $this->variableArray;
    }

    /**
     * Gets a specific variable from the frame.
     * @param string $name The name of the variable.
     * @return VariableTypeData|null The data of the variable, or null if not found.
     */
    public function getVariable(string $name): ?VariableTypeData{
        return $this->variableArray[$name] ?? null;
    }

     /**
     * Sets the data of a variable in the frame.
     * @param string $name The name of the variable.
     * @param VariableTypeData $variableTypeData The type and value of the variable.
     * @return void
     */
    public function setVariable(string $name, VariableTypeData $variableTypeData): void{
        $this->variableArray[$name] = $variableTypeData;
    }

}