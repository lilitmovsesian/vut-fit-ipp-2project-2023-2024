<?php

namespace IPP\Student;

use Exception;
use IPP\Core\AbstractInterpreter;

/**
 * Class Interpreter
 * Represents an interpreter that executes a set of instructions.
 */
class Interpreter extends AbstractInterpreter
{
    /**
    * @var array<int, Instruction> $instructions
    */
    private array $instructions;
    private ?int $instructionPointer;
    /**
    * @var array<string, int> $labels
    */
    private array $labels;
    private Frame $globalFrame;
    private ?Frame $temporaryFrame;
    /**
    * @var array<Frame> $frameStack
    */
    private array $frameStack;
    /**
    * @var array<?int> $callStack
    */
    private array $callStack;
    /**
    * @var array<VariableTypeData> $dataStack
    */
    private array $dataStack;

    public function execute(): int
    {
        /* Initializes variables.*/
        $this->instructionPointer = 0;
        $this->instructions = array();
        $this->labels = array();
        $this->globalFrame = new Frame();
        /* Temporary frame is not set.*/
        $this->temporaryFrame = NULL;
        /* Stacks arrays.*/
        $this->frameStack = array();
        $this->callStack = array();
        $this->dataStack = array();  

        $dom = $this->source->getDOMDocument();
        $instructionParser = new InstructionParser();
        /* Gets instruction array using InstructionParser class method.*/
        $this->instructions = $instructionParser->parse($dom);
        $this->labels =  $instructionParser->getLabels();

        /* Sets instruction pointer to the first order. */
        $this->instructionPointer = !empty($this->instructions) ? min(array_keys($this->instructions)) : null;
        
        /* A loop through instructions based on the instruction orders. */
        while ($this->instructionPointer !== null) {
            $instruction = $this->instructions[$this->instructionPointer];
            if ($instruction->opcode === 'DEFVAR'){
                $this->defVarInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'CREATEFRAME'){
                $this->createFrameInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'PUSHFRAME'){
                $this->pushFrameInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'POPFRAME'){
                $this->popFrameInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'MOVE'){
                $this->moveInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'CALL'){
                $this->callInstruction($instruction);
            }
            else if ($instruction->opcode === 'JUMP'){
                $this->jumpInstruction($instruction);
            }
            else if ($instruction->opcode === 'RETURN'){
                $this->returnInstruction($instruction);
            }
            else if ($instruction->opcode === 'PUSHS'){
                $this->pushsInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'POPS'){
                $this->popsInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'ADD'){
                $this->addInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'SUB'){
                $this->subInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'MUL'){
                $this->mulInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'IDIV'){
                $this->idivInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'LT'){
                $this->ltInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'GT'){
                $this->gtInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'EQ'){
                $this->eqInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'AND'){
                $this->andInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'OR'){
                $this->orInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'NOT'){
                $this->notInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'INT2CHAR'){
                $this->int2charInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'STRI2INT'){
                $this->stri2intInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'READ'){
                $this->readInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'WRITE'){
                $this->writeInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'CONCAT'){
                $this->concatInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'STRLEN'){
                $this->strlenInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'GETCHAR'){
                $this->getcharInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'SETCHAR'){
                $this->setcharInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'LABEL'){
                $this->labelInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'TYPE'){
                $this->typeInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'JUMPIFEQ'){
                $this->jumpifeqInstruction($instruction);
            }
            else if ($instruction->opcode === 'JUMPIFNEQ'){
                $this->jumpifneqInstruction($instruction);
            }
            else if ($instruction->opcode === 'EXIT'){
                $this->exitInstruction($instruction);
            }
            else if ($instruction->opcode === 'DPRINT'){
                $this->dprintInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else if ($instruction->opcode === 'BREAK'){
                $this->breakInstruction($instruction);
                $this->instructionPointer = $this->getNextOrder($instruction);
            }
            else {
                throw new StudentException(32);
            }
        }
        return 0;
    }

    /**
     * A method to get next instruction order (loops through each order 
     * and finds the first order greater than the current one).
     * @param Instruction $instruction The current instruction object.
     * @return int|null The order of the next instruction or null.
     */
    private function getNextOrder(Instruction $instruction): ?int {
        $nextOrder = null;
        foreach ($this->instructions as $order => $instr) {
            if ($order > $instruction->order) {
                $nextOrder = $order;
                break;
            }
        }
        return $nextOrder;
    }
    
    /**
     * A method for defvar instruction. Validates the arguments of the instruction.
     * Explodes a frame name of the variable and invokes the Frame methods
     * getVariable and addVariable based on the frame. 
     * @param Instruction $instruction The instruction object.
     */
    private function defvarInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
    
        if ($instruction->arguments[0]->type !== 'var') {
            throw new StudentException(53);
        }
    
        $varParts = explode('@', $instruction->arguments[0]->value);
        if (count($varParts) !== 2) {
            throw new StudentException(52);
        }
        list($frame, $name) = $varParts;
        if ($frame === 'GF') {
            if ($this->globalFrame->getVariable($name)) {
                throw new StudentException(52);
            }
            $this->globalFrame->addVariable($name);
        } else if ($frame === 'LF') {
            if (count($this->frameStack) === 0) {
                throw new StudentException(55);
            }
            if (end($this->frameStack)->getVariable($name)) {
                throw new StudentException(52);
            }
            end($this->frameStack)->addVariable($name);
        } else if ($frame === 'TF') {
            if ($this->temporaryFrame === null) {
                throw new StudentException(55);
            }
            if ($this->temporaryFrame->getVariable($name)) {
                throw new StudentException(52);
            }
            $this->temporaryFrame->addVariable($name);
        } else {
            throw new StudentException(32);
        }
    }

    /**
     * A method for createframe instruction.
     * Creates a new temporary frame.
     * Validates the arguments of an instruction.
     * @param Instruction $instruction The instruction object.
     */
    private function createFrameInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 0) {
            throw new StudentException(32);
        }
        $this->temporaryFrame = new Frame();
    }
    
    /**
     * A method for pushframe instruction.
     * Pushs a temporary frame to the stack of local frames.
     * Validates the arguments of an instruction.
     * @param Instruction $instruction The instruction object.
     */
    private function pushFrameInstruction(Instruction $instruction): void {
        if ($this->temporaryFrame === null) {
            throw new StudentException(55);
        }
        if (count($instruction->arguments) !== 0) {
            throw new StudentException(32);
        }
        $this->frameStack[] = $this->temporaryFrame;
        $this->temporaryFrame = null;
    }
    
    /**
     * A method for popframe instruction.
     * Pops a local frames from the stack and sets a temporary frame.
     * Validates the arguments of an instruction.
     * @param Instruction $instruction The instruction object.
     */
    private function popFrameInstruction(Instruction $instruction): void {
        if (empty($this->frameStack)) {
            throw new StudentException(55);
        }
        if (count($instruction->arguments) !== 0) {
            throw new StudentException(32);
        }
        $this->temporaryFrame = array_pop($this->frameStack);
    }
    
    /**
     * A method for move instruction. Validates the arguments of the instruction.
     * Moves the value of one symbol to another variable using helper functions.
     * @param Instruction $instruction The instruction object.
     */
    private function moveInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 2 || $instruction->arguments[0] === null || $instruction->arguments[1] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' || !$this->isASymbol($instruction->arguments[1])) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper function. */ 
        $value = $this->readSymbol($instruction->arguments[1]);
        /*Handles an empty int and sets the value to 0.*/
        if ($value !== null) {
            if ($value->type === 'int' && $value->value === '') {
                $value->value = '0';
            }
            if ($value->type === 'type') {
                $value->type = 'string';
            }
            $this->writeVariable($instruction->arguments[0], $value);
        }
    }
    
    /**
     * A method for call instruction. Validates the arguments of the instruction.
     * Pushes the next instruction order to the call stack and jumps to the label.
     * @param Instruction $instruction The instruction object.
     */
    private function callInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'label') {
            throw new StudentException(53);
        }
        $nextOrder = $this->getNextOrder($instruction);
        $this->callStack[] = $nextOrder;
        $this->jumpLabel($instruction->arguments[0]);
    }
    
     /**
     * A method for return instruction. Validates the arguments of the instruction.
     * Pops an order from the call stack and assigns the instruction pointer to it.
     * @param Instruction $instruction The instruction object.
     */
    private function returnInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 0) {
            throw new StudentException(32);
        }
        if (empty($this->callStack)) {
            throw new StudentException(56);
        }
        $this->instructionPointer = array_pop($this->callStack);
    }
    
    /**
     * A method for pushs instruction. Validates the arguments of the instruction.
     * Reads a symbol data and pushs it to the data stack.
     * @param Instruction $instruction The instruction object.
     */
    private function pushsInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if (!$this->isASymbol($instruction->arguments[0])) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper function. */ 
        $symbol = $this->readSymbol($instruction->arguments[0]);
        if ($symbol !== null) {
            $this->dataStack[] = $symbol;
        }
    }
    
    /**
     * A method for pops instruction. Validates the arguments of the instruction.
     * Pops data from the data stack and assigns the variable.
     * @param Instruction $instruction The instruction object.
     */
    private function popsInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var') {
            throw new StudentException(53);
        }
        if (empty($this->dataStack)) {
            throw new StudentException(56);
        }
        $data = array_pop($this->dataStack);
        $this->writeVariable($instruction->arguments[0], $data);
    }

    /**
     * A method for add instruction.
     * Performs the arithmetic addition of two symbols and assigns a variable.
     * @param Instruction $instruction The instruction object.
     */
    private function addInstruction(Instruction $instruction): void {
        $this->arithmeticOperandsValidation($instruction);
        $value1 = null;
        $value2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $value1 = $this->readSymbol($instruction->arguments[1]);
            $value2 = $this->readSymbol($instruction->arguments[2]);
        }    
        $sum = VariableTypeData::createEmpty();
        $sum->type = 'int';
        if ($value1 !== null && $value2 !== null && $instruction->arguments[0] !== null){
            /* Converts strings to integers, performs addition and converts the result to string. */
            $sum->value = strval(intval($value1->value) + intval($value2->value));
            $this->writeVariable($instruction->arguments[0], $sum);
        }
    }
    
     /**
     * A method for sub instruction.
     * Performs the arithmetic subtraction of two symbols and assigns a variable.
     * @param Instruction $instruction The instruction object.
     */
    private function subInstruction(Instruction $instruction): void {
        $this->arithmeticOperandsValidation($instruction);
        $value1 = null;
        $value2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $value1 = $this->readSymbol($instruction->arguments[1]);
            $value2 = $this->readSymbol($instruction->arguments[2]);
        }    
        $dif = VariableTypeData::createEmpty();
        $dif->type = 'int';
        if ($value1 !== null && $value2 !== null && $instruction->arguments[0] !== null){
            $dif->value =  strval(intval($value1->value) -  intval($value2->value));
            $this->writeVariable($instruction->arguments[0], $dif);
        }
    }
    
    /**
     * A method for mul instruction.
     * Performs the arithmetic multiplication of two symbols and assigns a variable.
     * @param Instruction $instruction The instruction object.
     */
    private function mulInstruction(Instruction $instruction): void {
        $this->arithmeticOperandsValidation($instruction);
        $value1 = null;
        $value2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $value1 = $this->readSymbol($instruction->arguments[1]);
            $value2 = $this->readSymbol($instruction->arguments[2]);
        }    
        $prod = VariableTypeData::createEmpty();
        $prod->type = 'int';
        if ($value1 !== null && $value2 !== null && $instruction->arguments[0] !== null){
            $prod->value = strval(intval($value1->value) *  intval($value2->value));
            $this->writeVariable($instruction->arguments[0], $prod);
        }
    }
    
    /**
     * A method for idiv instruction.
     * Performs the arithmetic division of two symbols and assigns a variable.
     * @param Instruction $instruction The instruction object.
     */
    private function idivInstruction(Instruction $instruction): void {
        $this->arithmeticOperandsValidation($instruction);
        $value1 = null;
        $value2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $value1 = $this->readSymbol($instruction->arguments[1]);
            $value2 = $this->readSymbol($instruction->arguments[2]);
        }
        $quot = VariableTypeData::createEmpty();
        $quot->type = 'int';
        /*Checks if the divider is not 0.*/
        if ($value1 !== null && $value2 !== null){
            if (intval($value2->value) === 0) {
                throw new StudentException(57);
            }
            $quot->value = strval(intval($value1->value) /  intval($value2->value));
            if ($instruction->arguments[0] !== null){
                $this->writeVariable($instruction->arguments[0], $quot);
            }
        }
    }
    
    /**
     * A method for lt instruction.
     * Checks if the first value is less than the second one and assigns a bool variable.
     * @param Instruction $instruction The instruction object.
     */
    private function ltInstruction(Instruction $instruction): void {
        $this->relationOperandsValidation($instruction);
        $value1 = null;
        $value2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $value1 = $this->readSymbol($instruction->arguments[1]);
            $value2 = $this->readSymbol($instruction->arguments[2]);
        }
    
        $res = VariableTypeData::createEmpty();
        $res->type = 'bool';
        $res->value = 'false';
        if ($value1 !== null && $value2 !== null){
            if ($value1->value < $value2->value) {
                $res->value = 'true';
            }
        }
        if ($instruction->arguments[0] !== null){
            $this->writeVariable($instruction->arguments[0], $res);
        }
    }

    /**
     * A method for gt instruction.
     * Checks if the first value is greater than the second one and assigns a bool variable.
     * @param Instruction $instruction The instruction object.
     */
    private function gtInstruction(Instruction $instruction): void {
        $this->relationOperandsValidation($instruction);
        $value1 = null;
        $value2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $value1 = $this->readSymbol($instruction->arguments[1]);
            $value2 = $this->readSymbol($instruction->arguments[2]);
        }
    
        $res = VariableTypeData::createEmpty();
        $res->type = 'bool';
        $res->value = 'false';
        if ($value1 !== null && $value2 !== null){
            if ($value1->value > $value2->value) {
                $res->value = 'true';
            }
        }
        if ($instruction->arguments[0] !== null){
            $this->writeVariable($instruction->arguments[0], $res);
        }
    }
    
    /**
     * A method for eq instruction.
     * Checks if the first value is equal to the second one and assigns a bool variable.
     * If the type of symbols are int, converts the string value to int.
     * @param Instruction $instruction The instruction object.
     */
    private function eqInstruction(Instruction $instruction): void {
        $this->equalityOperandsValidation($instruction);
        $symbol1 = null;
        $symbol2 = null;
        if ($instruction->arguments[1] !== null && $instruction->arguments[2] !== null) {
            /*Retrieves data using helper functions. */ 
            $symbol1 = $this->readSymbol($instruction->arguments[1]);
            $symbol2 = $this->readSymbol($instruction->arguments[2]);
        }
        $res = VariableTypeData::createEmpty();
        $res->type = 'bool';
        $res->value = 'false';
        if ($symbol1 !== null && $symbol2 !== null){
            $value1 = $symbol1->value;
            $value2 = $symbol2->value;
            if ($symbol1->type === 'int') { 
                $value1 = intval($symbol1->value);
            }
            if ($symbol2->type === 'int') { 
                $value2 = intval($symbol2->value);
            }
            if ($value1 === $value2) {
                $res->value = 'true';
            }
        }
        if ($instruction->arguments[0] !== null){
            $this->writeVariable($instruction->arguments[0], $res);
        }
    }

    /**
     * A method for and instruction. Validates the instruction arguments.
     * Performs a logical and operation on two bool symbols and assigns a bool variable.
     * @param Instruction $instruction The instruction object.
     */
    private function andInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'bool') ||
            !$this->isASymbolOfType($instruction->arguments[2], 'bool')) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper functions. */ 
        $value1 = $this->readSymbol($instruction->arguments[1]);
        $value2 = $this->readSymbol($instruction->arguments[2]);
        $res = VariableTypeData::createEmpty();
        $res->type = 'bool';
        $res->value = 'false';
        if ($value1 !== null && $value2 !== null){
            if (strtoupper($value1->value) === 'TRUE' && strtoupper($value2->value) === 'TRUE'){
                $res->value = 'true';
            }
        }
        $this->writeVariable($instruction->arguments[0], $res);
    }
    
    /**
     * A method for or instruction. Validates the instruction arguments.
     * Performs a logical or operation on two bool symbols and assigns a bool variable.
     * @param Instruction $instruction The instruction object.
     */
    private function orInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'bool') ||
            !$this->isASymbolOfType($instruction->arguments[2], 'bool')) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper functions. */ 
        $value1 = $this->readSymbol($instruction->arguments[1]);
        $value2 = $this->readSymbol($instruction->arguments[2]);
        $res = VariableTypeData::createEmpty();
        $res->type = 'bool';
        $res->value = 'false';
        if ($value1 !== null && $value2 !== null){
            if (strtoupper($value1->value) === 'TRUE' || strtoupper($value2->value) === 'TRUE'){
                $res->value = 'true';
            }    
        }
        $this->writeVariable($instruction->arguments[0], $res);
    }
    
    /**
     * A method for not instruction. Validates the instruction arguments.
     * Performs a logical not operation on the bool symbol and assigns a bool variable.
     * @param Instruction $instruction The instruction object.
     */
    private function notInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 2 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'bool')){
            throw new StudentException(53);
        }
        /*Retrieves data using helper function. */ 
        $value = $this->readSymbol($instruction->arguments[1]);
        $res = VariableTypeData::createEmpty();
        $res->type = 'bool';
        if ($value !== null) {
            if (strtoupper($value->value) === 'TRUE'){
                $res->value = 'false';
            }
            else if (strtoupper($value->value) === 'FALSE'){
                $res->value = 'true';
            }
            $this->writeVariable($instruction->arguments[0], $res);
        }
    }
    
    /**
     * A method for int2char instruction. Validates the instruction arguments.
     * Gets an integer value in a specified integer symbol and stores its
     * corresponding Unicode character into a variable.
     * @param Instruction $instruction The instruction object.
     */
    private function int2CharInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 2 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' || 
            !$this->isASymbolOfType($instruction->arguments[1], 'int')) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper function. */ 
        $intSymbol = $this->readSymbol($instruction->arguments[1]);
        $charValue = null;
        if ($intSymbol !== null){
            try {
                /* Validates that integer is inside the range and converts it
                to a Unicode string. */
                if ($intSymbol->value >= 0x0 && $intSymbol->value <= 0x10FFFF) {
                    $charValue = chr(intval($intSymbol->value));
                }
                else {
                    throw new StudentException(58);
                }
            }
            catch (Exception $ex) {
                throw new StudentException(58);
            }
            /* Creates new Variable structure and assigns a variable. */
            $charVar = new VariableTypeData('string', $charValue);
            $this->writeVariable($instruction->arguments[0], $charVar);
        }
    }

    /**
     * A method for stri2int instruction. Validates the instruction arguments.
     * Gets an ordinal value of character of a specified string symbol at a 
     * specified index and stores it into an integer variable.
     * @param Instruction $instruction The instruction object.
     */
    private function stri2intInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' || 
            !$this->isASymbolOfType($instruction->arguments[1], 'string') 
            || !$this->isASymbolOfType($instruction->arguments[2], 'int')) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper functions. */ 
        $value1 = $this->readSymbol($instruction->arguments[1]);
        $value2 = $this->readSymbol($instruction->arguments[2]);
        if ($value1 !== null && $value2 !== null){
            /* Validates the index. */
            if (intval($value2->value) < 0 || intval($value2->value) >= strlen($value1->value)){
                throw new StudentException(58);
            }
            /* Gets the ordinal value of the character on the specified index.*/
            $intValue = ord($value1->value[intval($value2->value)]);
            /* Creates new Variable structure and assigns a variable. */
            $intVar = new VariableTypeData('int', strval($intValue));
            $this->writeVariable($instruction->arguments[0], $intVar);
        }
    }
    
    /**
     * A method for read instruction. Validates the instruction arguments.
     * Reads value of specified type from a stdin and assignes a var.
     * @param Instruction $instruction The instruction object.
     */
    private function readInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 2 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' || 
        !$this->isAType($instruction->arguments[1])) {
            throw new StudentException(53);
        }
        /*Reads a value.*/
        $value = $this->input->readString();
        if ($value !== null){
            $value = explode("\n", $value)[0];
        }
        $res = null;
        if ($value !== null){
            /* If an input is empty sets an empty string if type is string, 
            otherwise nil values. */
            if ($value === ''){
                if (strtoupper($instruction->arguments[1]->value) === 'STRING'){
                    $res = new VariableTypeData('string', '');
                    $this->writeVariable($instruction->arguments[0], $res);
                }
                else if (strtoupper($instruction->arguments[1]->value) === 'BOOL' ||
                    strtoupper($instruction->arguments[1]->value) === 'INT' ){
                    $res = new VariableTypeData('nil', 'nil');
                    $this->writeVariable($instruction->arguments[0], $res);
                }
            }
            /*If input is not empty.*/
            else {
                $res = VariableTypeData::createEmpty();
                $res->type = strtolower($instruction->arguments[1]->value);
                if ($value !== null){
                    $res->value = $value;
                }
                if (strtoupper($instruction->arguments[1]->value) === 'INT'){
                    try {
                        /* If type is int, converts value to int. */
                        $intValue = intval($value);
                        $res->value = strval($intValue);
                        /*Handles case, when 0 is assigned to int despite the input was not 0.*/
                        if ($intValue === 0 && $value !== '0') {
                            $res->type = 'nil';
                            $res->value = 'nil';
                        }
                    }
                    catch (Exception $ex) {
                        $res->type = 'nil';
                        $res->value = 'nil';
                    }
                }
                /*If type is bool, set true, false or nil based on the input. */
                if (strtoupper($instruction->arguments[1]->value) === 'BOOL'){
    
                    if (strtoupper($value) === 'TRUE') {
                        $res->value = 'true';
                    }
                    else if (strtoupper($value) === 'FALSE') {
                        $res->value = 'false';
                    }
                    else {
                        $res->type = 'nil';
                        $res->value = 'nil';
                    }
                }
            }
        }
        /* If no input, sets nil. */
        else {
            $res = new VariableTypeData('nil', 'nil');
        }
        if ($res !== null){
            $this->writeVariable($instruction->arguments[0], $res);
        }
    }
    
    /**
     * A method for write instruction. Validates the instruction arguments.
     * Writes a value specified in a symbol to the stdout.
     * @param Instruction $instruction The instruction object.
     */
    private function writeInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if (!$this->isASymbol($instruction->arguments[0])) {
            throw new StudentException(53);
        }
        $symbol = $this->readSymbol($instruction->arguments[0]);
        if ($symbol !== null){
            $value = $symbol->value;
            if ($symbol->type === 'nil'){
                $value = '';
            }
            /* If a symbol is of type string, handles escape sequences. */
            if ($symbol->type === 'string'){
                $value = $this->parseEscape($value);
            }
            /* If a symbol is of type int and is empty, sets 0 to the value. */
            if ($symbol->type === 'int' && $value === ''){
                $value = 0;
            }
            $this->stdout->writeString("$value");
        }
    }
    
    /**
     * A method for concat instruction. Validates the instruction arguments.
     * Retrieves strings of specified symbols, concat and assigns a var.
     * @param Instruction $instruction The instruction object.
     */
    private function concatInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'string') ||
            !$this->isASymbolOfType($instruction->arguments[2], 'string')) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper functions. */ 
        $value1 = $this->readSymbol($instruction->arguments[1]);
        $value2 = $this->readSymbol($instruction->arguments[2]);
        if ($value1 !== null && $value2 !== null){
            $strValue = $value1->value.$value2->value;
            $strVar = new VariableTypeData('string', $strValue);
            $this->writeVariable($instruction->arguments[0], $strVar);
        }
    }
    
    /**
     * A method for strlen instruction. Validates the instruction arguments.
     * Assigns variable to the integer of a strlen of a specified string symbol.
     * @param Instruction $instruction The instruction object.
     */
    private function strlenInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 2 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'string')){
            throw new StudentException(53);
        }
        $value = $this->readSymbol($instruction->arguments[1]);
        if ($value !== null) {
            $intVar = new VariableTypeData('int', strval(strlen($value->value)));
            $this->writeVariable($instruction->arguments[0], $intVar);
        }
    }
    
    /**
     * A method for setchar instruction. Validates the instruction arguments.
     * Changes a character of a string variable at a specific index from an int symbol to
     * the first character of a string symbol.
     * @param Instruction $instruction The instruction object.
     */
    private function setcharInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'int') ||
            !$this->isASymbolOfType($instruction->arguments[2], 'string')) {
            throw new StudentException(53);
        }
        /*Retrieves data using helper functions. */ 
        $var = $this->readVariable($instruction->arguments[0]);
        $indexSymbol = $this->readSymbol($instruction->arguments[1]);
        $stringSymbol = $this->readSymbol($instruction->arguments[2]);
        if ($var !== null && $indexSymbol !== null && $stringSymbol !== null){
            if ($var->type !== 'string'){
                throw new StudentException(53);
            }
            $string = $stringSymbol->value;
            $stringVar = $var->value;
            $index = $indexSymbol->value;
            /* Validates the index. */
            if (strlen($string) === 0 || intval($index) < 0 || intval($index) >= strlen($stringVar)){
                throw new StudentException(58);
            }
            $stringVar[intval($index)] = $string[0];
            $newVar = new VariableTypeData('string', $stringVar);
            $this->writeVariable($instruction->arguments[0], $newVar);
        }
    }
    
    /**
     * A method for getchar instruction. Validates the instruction arguments.
     * Retrieves a character at a specific index from an int symbol of a string symbol and 
     * stores this character in variable.
     * @param Instruction $instruction The instruction object.
     */
    private function getcharInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' || 
            !$this->isASymbolOfType($instruction->arguments[1], 'string') 
            || !$this->isASymbolOfType($instruction->arguments[2], 'int')) {
            throw new StudentException(53);
        }
         /*Retrieves data using helper functions. */ 
        $value1 = $this->readSymbol($instruction->arguments[1]);
        $value2 = $this->readSymbol($instruction->arguments[2]);
        if ($value1 !== null && $value2 !== null){
            /* Validates the index. */
            if (intval($value2->value) < 0 || intval($value2->value) >= strlen($value1->value)){
                throw new StudentException(58);
            }
            $strVar = new VariableTypeData('string', $value1->value[intval($value2->value)]);
            $this->writeVariable($instruction->arguments[0], $strVar);
        }
    }
    
    /**
     * A method for type instruction. Validates the instruction arguments and does nothing.
     * @param Instruction $instruction The instruction object.
     */
    private function labelInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'label') {
            throw new StudentException(53);
        }
    }
    
    /**
     * A method for type instruction. Validates the instruction arguments.
     * Retrieves a type of specified symbol and assigns a variable to a string
     * of this type.
     * @param Instruction $instruction The instruction object.
     */
    private function typeInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 2 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbol($instruction->arguments[1])){
            throw new StudentException(53);
        }
        $type = $this->readSymbolType($instruction->arguments[1]);
        $typeVar = new VariableTypeData('string', $type);
        $this->writeVariable($instruction->arguments[0], $typeVar);
    }
    
     /**
     * A method for jump instruction. Validates the instruction arguments.
     * Jumps to the specified label (sets the instruction pointer to the order of label).
     * @param Instruction $instruction The instruction object.
     */
    private function jumpInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'label') {
            throw new StudentException(53);
        }
        $this->jumpLabel($instruction->arguments[0]);
    }
    
     /**
     * A method for jumpifeq instruction. Validates the instruction arguments.
     * If values are equal and both are not null, jumps to the specified label.
     *  If values are not equal, moves to the next instruction.
     * @param Instruction $instruction The instruction object.
     */
    private function jumpifeqInstruction(Instruction $instruction): void{
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'label' ||
        !$this->isTheCompatibleType($instruction->arguments[1],$instruction->arguments[2])) {
            throw new StudentException(53);
        }
        $value1 = null;
        $value2 = null;
        $symbol1 = ($this->readSymbol($instruction->arguments[1]));
        $symbol2 = ($this->readSymbol($instruction->arguments[2]));
        /* Converts symbol values to integers if they are of type 'int'.*/
        if ($symbol1 !== null){
            $value1 = $symbol1->value;
            if ($symbol1->type === 'int') { 
                $value1 = intval($symbol1->value);
            }
        }
        if ($symbol2 !== null){
            $value2 = $symbol2->value;
            if ($symbol2->type === 'int') { 
                $value2 = intval($symbol2->value);
            }
        }
        if ($value1 === $value2 && $value1 !== null  && $value2 !== null) {
            $this->jumpLabel($instruction->arguments[0]);
        }
        else {
            $this->instructionPointer = $this->getNextOrder($instruction);
        }
    }
    
    /**
     * A method for jumpifneq instruction. Validates the instruction arguments.
     * If values are not equal and both are not null, jumps to the specified label.
     *  If values are equal, moves to the next instruction.
     * @param Instruction $instruction The instruction object.
     */
    private function jumpifneqInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null 
        || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(32);
        }
        if ($instruction->arguments[0]->type !== 'label' ||
        !$this->isTheCompatibleType($instruction->arguments[1],$instruction->arguments[2])) {
            throw new StudentException(53);
        }
        $value1 = null;
        $value2 = null;
        $symbol1 = $this->readSymbol($instruction->arguments[1]);
        $symbol2 = $this->readSymbol($instruction->arguments[2]);
        /* Converts symbol values to integers if they are of type 'int'.*/
        if ($symbol1 !== null){
            $value1 = $symbol1->value;
            if ($symbol1->type === 'int') { 
                $value1 = intval($symbol1->value);
            }
        }
        if ($symbol2 !== null){
            $value2 = $symbol2->value;
            if ($symbol2->type === 'int') { 
                $value2 = intval($symbol2->value);
            }
        }
        if ($value1 !== $value2 && $value1 !== null  && $value2 !== null) {
            $this->jumpLabel($instruction->arguments[0]);
        }
        else {
            $this->instructionPointer = $this->getNextOrder($instruction);
        }
    }
    
    /**
     * A method for exit instruction. Validates the instruction arguments.
     * Exits with a return code specified in a symbol if the value is a velue
     * between 0 and 9.
     * @param Instruction $instruction The instruction object.
     */
    private function exitInstruction(Instruction $instruction): void{
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        if (!$this->isASymbolOfType($instruction->arguments[0], 'int')) {
            throw new StudentException(53);
        }
        $symb = $this->readSymbol($instruction->arguments[0]);
        if ($symb !== null) {
            $rc = intval($symb->value);
            if(intval($rc) < 0 || intval($rc)>9){
                throw new StudentException(57);
            }
            exit($rc);
        }
    }
    
    /**
     * A method for dprint instruction. Validates the arguments of the instruction.
     * Writes specified symbol to the stderr.
     * @param Instruction $instruction The instruction object.
     */
    private function dprintInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 1 || $instruction->arguments[0] === null) {
            throw new StudentException(32);
        }
        
        if (!$this->isASymbol($instruction->arguments[0])) {
            throw new StudentException(53);
        }
        $value = null;
        $valueSymbol = ($this->readSymbol($instruction->arguments[0]));
        if ($valueSymbol !== null) {
            $value = $valueSymbol->value;
        }
        $this->stderr->writeString("$value");
    }

    /**
     * A method for break instruction. Validates the arguments of the instruction.
     * Writes to the stderr the current status of the interpreter.
     * @param Instruction $instruction The instruction object.
     */
    private function breakInstruction(Instruction $instruction): void {
        if (count($instruction->arguments) !== 0) {
            throw new StudentException(32);
        }
        $pointer = $this->instructionPointer;
        $lfStackSize = count($this->frameStack);
        $this->stderr->writeString("Instruction pointer is currently at $pointer order.\n");
        $this->stderr->writeString("There are $lfStackSize local frames in a stack.\n");
        $this->stderr->writeString("Global frame variables:\n");
        foreach ($this->globalFrame->getVariableList() as $variableName => $variable){
            $this->stderr->writeString("$variableName => $variable->type, $variable->value\n");
        }
        $this->stderr->writeString("Temporary frame variables:\n");
        if ($this->temporaryFrame !== null) {
            foreach ($this->temporaryFrame->getVariableList() as $variableName => $variable){
                $this->stderr->writeString("$variableName => $variable->type, $variable->value\n");
            }
        }
        $this->stderr->writeString("Local frame variables:\n");
        if (!empty($this->frameStack)) {
            foreach (end($this->frameStack)->getVariableList() as $variableName => $variable){
                $this->stderr->writeString("$variableName => $variable->type, $variable->value\n");
            }
        }
    }

    /**
     * A helper method to jump to label (sets instruction pointer ro an order 
     * of the specified label).
     * @param Argument $labelArgument The instruction argument object.
     */
    private function jumpLabel(Argument $labelArgument): void {
        if (!isset($this->labels[$labelArgument->value])) {
            throw new StudentException(52);
        }
        $this->instructionPointer = $this->labels[$labelArgument->value];
    }

    /**
     * A helper method to get a type of the symbol.
     * In case an argument is a variable, the type is retrieved using
     * a helper method for reading variable data. In case the argument 
     * is a constant, a type is retrieved directly from the argument.
     * @param Argument $symbolArgument The instruction argument object.
     * @return string The type to return.
     */
    private function readSymbolType(Argument $symbolArgument): string {
        $type = '';
        if ($symbolArgument->type === 'var') {
            $value = $this->readVariable($symbolArgument);
            if ($value === null) {
                throw new StudentException(54);
            }
            if ($value->type === '' && $value->value === '') {
                $str = '';
                return $str;
            }
            return $value->type;
        }
        if ($this->isAConstant($symbolArgument)) {
            return $symbolArgument->type;
        }
        return $type;
    }

    /**
     * A helper method to parse escape sequences.
     * Checks if the current character is a backslash and the next three characters are numeric,
     * extracts the numeric value, adds the numeric values of escape sequences and the ASCII 
     * values of the other characters to the array, returns a string
     * @param string $string The string with escape sequences.
     * @return string  The result string to return.
     */
    private function parseEscape(string $string): string {
        $arr = [];
        $i = 0;
        while ($i < strlen($string)) {
            if ($string[$i] === '\\' && isset($string[$i + 1], $string[$i + 2], $string[$i + 3]) &&
                is_numeric($string[$i + 1]) && is_numeric($string[$i + 2]) && is_numeric($string[$i + 3])) {
                
                $num = intval(substr($string, $i + 1, 3)); 
                $arr[] = $num;
                $i += 4;
            } else {
                $arr[] = ord($string[$i]);
                $i++;
            }
        }
        return pack('C*', ...$arr);
    }
    
    /**
     * A helper method to check if the argument is of type 'type'.
     * @param Argument $argument The instruction argument object.
     * @return bool  The result bool value to return.
     */
    private function isAType(Argument $argument): bool {
        if ($argument->type === 'type' &&
            (strtoupper($argument->value) === 'STRING' ||
            strtoupper($argument->value) === 'BOOL' ||
            strtoupper($argument->value) === 'INT')) {
            return true;
        }
        return false;
    }

    /**
     * A helper method fot validation of the arguments of the arithmetic instructions.
     * @param Instruction $instruction The instruction object.
     */
    private function arithmeticOperandsValidation(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(52);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isASymbolOfType($instruction->arguments[1], 'int') ||
            !$this->isASymbolOfType($instruction->arguments[2], 'int')) {
            throw new StudentException(53);
        }
    }

    /**
     * A helper method fot validation of the arguments of the relation instructions.
     * @param Instruction $instruction The instruction object.
     */
    private function relationOperandsValidation(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(52);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isTheSameType($instruction->arguments[1], $instruction->arguments[2])) {
            throw new StudentException(53);
        }
    }

     /**
     * A helper method fot validation of the arguments of the eq instruction.
     * @param Instruction $instruction The instruction object.
     */
    private function equalityOperandsValidation(Instruction $instruction): void {
        if (count($instruction->arguments) !== 3 || $instruction->arguments[0] === null || $instruction->arguments[1] === null || $instruction->arguments[2] === null) {
            throw new StudentException(52);
        }
        if ($instruction->arguments[0]->type !== 'var' ||
            !$this->isTheCompatibleType($instruction->arguments[1], $instruction->arguments[2])) {
            throw new StudentException(53);
        }  
    }

    /**
     * A helper method to check if the two arguments are of the same type.
     * @param Argument $argument1 The instruction argument object.
     * @param Argument $argument2 The instruction argument object.
     * @return bool  The result bool value to return.
     */
    private function isTheSameType(Argument $argument1, Argument $argument2): bool {
        [$type1, $type2] = $this->getTypes($argument1, $argument2);
        if ($type1 !== '' && $type2 !== '' && $type1 === $type2 && $type1 !== 'nil' && $type2 !== 'nil') {
            return true;
        }
        return false;
    }

    /**
     * A helper method to check if the two arguments are of compatible types
     * (of the same types or one type is nil).
     * @param Argument $argument1 The instruction argument object.
     * @param Argument $argument2 The instruction argument object.
     * @return bool  The result bool value to return.
     */
    private function isTheCompatibleType(Argument $argument1, Argument $argument2): bool {
        [$type1, $type2] = $this->getTypes($argument1, $argument2);
        if (($type1 !== '' && $type2 !== '' && $type1 === $type2) || $type1 === 'nil' || $type2 === 'nil') {
            return true;
        }
        return false;
    }


     /**
     * A helper method to get types of the two arguments.
     * In case an argument is a variable, the type is retrieved using
     * a helper method for reading variable data. In case the argument 
     * is a constant, a type is retrieved directly from the argument.
     * @param Argument $argument1 The instruction argument object.
     * @param Argument $argument2 The instruction argument object.
     * @return array<string>  The result array of types to return.
     */
    private function getTypes(Argument $argument1, Argument $argument2): array {
        $type1 = '';
        $type2 = '';
        if ($argument1->type === 'var') {
            $value1 = $this->readVariable($argument1);
            if ($value1 !== null) {
                $type1 = $value1->type;
            }
        }
        else if ($argument1->type !== 'var') {
            $type1 = $argument1->type;
        }
        if ($argument2->type === 'var') {
            $value2 = $this->readVariable($argument2);
            if ($value2 !== null) {
                $type2 = $value2->type;
            }
        }
        else if ($argument2->type !== 'var') {
            $type2 = $argument2->type;
        }
        return [$type1, $type2];
    }

    /**
     * A helper method to check if the argument is a symbol (var or constant).
     * @param Argument $argument The instruction argument object.
     * @return bool  The result bool value to return.
     */
    private function isASymbol(Argument $argument): bool {
        if ($argument->type === 'var' ||
            $this->isAConstant($argument)) {
                return true;
        }
        return false;
    }

    /**
     * A helper method to check if the argument is a constant.
     * @param Argument $argument The instruction argument object.
     * @return bool  The result bool value to return.
     */
    private function isAConstant(Argument $argument): bool {
        if ($argument->type === 'int' ||
            $argument->type === 'bool' ||
            $argument->type === 'nil' ||
            $argument->type === 'string') {
                return true;
        }
        return false;
    }

    /**
     * A helper method to check if the argument is a symbol of the specified type.
     * @param Argument $argument The instruction argument object.
     * @param string $type The specified type.
     * @return bool  The result bool value to return.
     */
    private function isASymbolOfType(Argument $argument, string $type): bool {
        if ($argument->type === 'var') {
            $value = $this->readVariable($argument);
            if ($value !== null) {
                if($value->type === $type) {
                    return true;
                }
            }
        }
        else if ($argument->type === $type) {
            return true;
        }
        return false;
    }

    /**
     * A helper method for getting a type and value of a variable.
     * In case an argument is a variable, the value is retrieved using
     * a helper method for reading variable data. In case the argument 
     * is a constant, a new instance of VariableTypeData is created.
     * @param Argument $symbolArgument The instruction argument object.
     * @return VariableTypeData|null  The variable data to return.
     */
    private function readSymbol(Argument $symbolArgument): ?VariableTypeData {
        if ($symbolArgument->type === 'var'){
            $value = $this->readVariable($symbolArgument);
            if ($value === null){
                throw new StudentException(54);
            }
            if ($value->type === '' && $value->value === '') {
                throw new StudentException(56);
            }
            return $value;
        }
        if ($this->isAConstant($symbolArgument)) {
            return new VariableTypeData($symbolArgument->type, $symbolArgument->value);
        }
        return null;
    }

    /**
     * A helper method for getting a type and value of a variable.
     * Explodes a frame name of the variable and invokes the Frame methods
     * getVariable based on the frame. 
     * @param Argument $variableArgument The instruction argument object.
     * @return VariableTypeData|null  The variable data to return.
     */
    private function readVariable(Argument $variableArgument): ?VariableTypeData {
        $varParts = explode('@', $variableArgument->value);
        if(count($varParts) !== 2) {
            throw new StudentException(32);
        }
        list($frame, $name) = $varParts;
        
        if ($frame === "GF") {
            return $this->globalFrame->getVariable($name);
        }
        else if ($frame === "TF") {
            if ($this->temporaryFrame === null){
                throw new StudentException(55);
            }
            return $this->temporaryFrame->getVariable($name);
        }
        else if ($frame === "LF") {
            if (count($this->frameStack) === 0) {
                throw new StudentException(55);
            }
            return end($this->frameStack)->getVariable($name);
        }
        else {
            throw new StudentException(32);
        }
    }

    /**
     * A helper method for setting a type and value to a variable.
     * Explodes a frame name of the variable and invokes the Frame methods
     * getVariable and setVariable based on the frame. 
     * @param Argument $variableArgument The instruction argument object.
     * @param VariableTypeData $variableTypeData The variable data to assign.
     */
    private function writeVariable(Argument $variableArgument, VariableTypeData $variableTypeData): void {
        $varParts = explode('@', $variableArgument->value);
        list($frame, $name) = $varParts;
        if ($frame === "GF") {
            if ($this->globalFrame->getVariable($name) === null){
                throw new StudentException(54);
            }
            $this->globalFrame->setVariable($name, $variableTypeData);
        }
        else if ($frame === "TF") {
            if ($this->temporaryFrame === null){
                throw new StudentException(55);
            }
            if ($this->temporaryFrame->getVariable($name) === null){
                throw new StudentException(54);
            }
            $this->temporaryFrame->setVariable($name, $variableTypeData);
        }
        else if ($frame === "LF") {
            if (count($this->frameStack) === 0) {
                throw new StudentException(55);
            }
            if (end($this->frameStack)->getVariable($name) === null){
                throw new StudentException(54);
            }
            end($this->frameStack)->setVariable($name, $variableTypeData);
        }
        else {
            throw new StudentException(32);
        }
    }
}