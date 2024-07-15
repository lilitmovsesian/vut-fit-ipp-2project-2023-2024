<?php

namespace IPP\Student;

/**
 * Class Instruction
 * Represents an instruction with an order, opcode, and arguments.
 */
class Instruction 
{
    public int $order;
    public string $opcode;
    /**
    * @var array<int, Argument|null> $arguments
     */
    public array $arguments;

     /**
     * Instruction constructor.
     * @param int $order The order of the instruction.
     * @param string $opcode The opcode of the instruction.
     * @param array<int, Argument> $arguments An array of arguments of the instruction.
     */
    public function __construct(int $order, string $opcode, array $arguments) {
        $this->order = $order;
        $this->opcode = $opcode;
        $this->arguments = $arguments;
    }
}