<?php

namespace IPP\Student;

/**
 * Class InstructionParser
 * Parses instructions from a given DOMDocument.
 */
class InstructionParser
{
    /**
    * @var array<string, int> $labels Holds all labels with their respective order.
    */
    private array $labels;

    /**
     * InstructionParser constructor.
     * Initializes labels array.
     */
    public function __construct() {
        $this->labels = array();
    }

    
    /**
     * Parses instructions from a DOMDocument.
     *
     * @param \DOMDocument $dom The DOMDocument containing instructions.
     * @return array<int, Instruction> Parsed instructions.
     */
    public function parse(\DOMDocument $dom): array{
        $tempInstructions = array();
        /*Gets the root.*/
        $root = $dom->documentElement;
        if ($root !== null) {
            if(strtoupper($root->getAttribute('language')) !== 'IPPCODE24'){
                throw new StudentException(32);
            }
        }
        else {
            throw new StudentException(32);
        }
        /* A loop through child nodes of the root element. Validates if the node is an 
        XMLElement, DOMElemment, and an 'instruction' element. Extract and validate the
        opcode and order of the instruction.*/
        if ($root !== null){
            foreach ($root->childNodes as $node) {
                if ($node->nodeType === XML_ELEMENT_NODE) {
                    if ($node instanceof \DOMElement) {
                        if ($node->nodeName === 'instruction'){
                            $opcode = strtoupper(trim($node->getAttribute('opcode')));
                            $this->opcodeValidate($opcode);
                            $order = trim($node->getAttribute('order'));
                            /*Ensure the opcode and order are not empty.*/
                            if (empty($opcode) || empty($order)) {
                                throw new StudentException(32);
                            }
                            /*Ensure order is unique and greater than 0.*/
                            if (isset($tempInstructions[intval($order)])) {
                                throw new StudentException(32);
                            }
                            if (intval($order) <= 0) {
                                throw new StudentException(32);
                            }
                            $arguments = array();

                            $argumentsLength = $node->childNodes->length;
                            /* A loop through child nodes to extract arguments. Validates if the node is an 
                             XMLElement, DOMElemment, and an 'arg' element.*/
                            foreach ($node->childNodes as $argumentElement) {
                                if ($argumentElement->nodeType === XML_ELEMENT_NODE) {
                                    if ($argumentElement instanceof \DOMElement){
                                        if (substr($argumentElement->nodeName, 0, 3) === 'arg') {
                                            $tag = trim($argumentElement->nodeName);
                                            /*Validates the format. */
                                            if (!preg_match('/^arg[1-3]$/', $tag)) {
                                                throw new StudentException(32);
                                            }
                                            /*Ensures an arg contains type. */
                                            if (!$argumentElement->hasAttribute('type')) {
                                                throw new StudentException(32);
                                            }
                                            /* Ensures index is valid and unique.*/
                                            $argumentIndex = (intval(substr($tag, 3)) - 1);
                                            if ($argumentIndex >= $argumentsLength) {
                                                throw new StudentException(32);
                                            }
                                            if (isset($arguments[$argumentIndex])) {
                                                throw new StudentException(32);
                                            }
                                            /* Extracts the type and value of the argument.*/
                                            $argumentType = $argumentElement->getAttribute('type');
                                            $argumentValue = !empty($argumentElement->textContent) ? trim($argumentElement->textContent) : '';
                                            $this->argumentValidate($argumentType, $argumentValue);
                                            /* If opcode is 'LABEL', ensures the label is unique and stores it.*/
                                            if ($opcode === 'LABEL'){
                                                if (isset($this->labels[$argumentValue])){
                                                    throw new StudentException(52);
                                                }
                                                $this->labels[$argumentValue] = intval($order);
                                            }
                                            /* Creates Argument object and adds it to the arguments array.*/
                                            $arguments[$argumentIndex] = new Argument($argumentType, $argumentValue);
                                        }
                                        else {
                                            throw new StudentException(32);
                                        }
                                    }
                                }
                            }
                            /*Creates Instruction object and adds it to the temporary instructions array.*/
                            $instruction = new Instruction(intval($order), $opcode, $arguments);
                            $tempInstructions[intval($order)] = $instruction;
                        }
                        else{
                            throw new StudentException(32);
                        }
                    }
                }
            }
        }
        /* Sorts according to order value and returns */
        ksort($tempInstructions);
        return $tempInstructions;
    }

    /**
     * Returns parsed labels array.
     * @return array<string, int>
    */
    public function getLabels(): array
    {
        return $this->labels;
    }

     /**
     * Validates the opcode format.
     *
     * @param string $opcode The opcode to validate.
     */
    private function opcodeValidate(string $opcode): void{
        $opcodePattern = '/^[A-Z0-9]+$/';
        if (!preg_match($opcodePattern, $opcode)){
            throw new StudentException(32);
        }
    } 

    /**
     * Validates an argument based on its type.
     *
     * @param string $argumentType The type of the argument.
     * @param string $argumentValue The value of the argument.
     */
    private function argumentValidate(string $argumentType, string $argumentValue): void{
        if ($argumentType === 'var'){
            $varPattern = '/[LTG]F@[A-Za-z_\-&%*$!?][A-Za-z0-9_\-&%*$!?]*/';
            if (!preg_match($varPattern, $argumentValue)){
                throw new StudentException(32);
            }
        }
        else if ($argumentType === 'label'){
            $labelPattern = '/^[A-Za-z_\-&%*$!?][A-Za-z0-9_\-&%*$!?]*$/';
            if (!preg_match($labelPattern, $argumentValue)){
                throw new StudentException(32);
            }
        }
        else if ($argumentType === 'type'){
            if (strtolower($argumentValue) !== 'nil' &&
            strtolower($argumentValue) !== 'string' &&
            strtolower($argumentValue) !== 'bool' &&
            strtolower($argumentValue) !== 'int'){
                throw new StudentException(32);
            }
        }
        else if ($argumentType === 'int'){
            $decPattern = '/^[-+]?[0-9]+$/';
            $octPattern = '/^[-+]?0[oO](_?[0-7])+$/';
            $hexPattern = '/^[-+]?0[xX](_?[0-9a-fA-F])+$/';

            if (!preg_match($decPattern, $argumentValue) && 
            !preg_match($octPattern, $argumentValue) && 
            !preg_match($hexPattern, $argumentValue) && 
            $argumentValue !== '') {
                throw new StudentException(32);
            }
        }
        else if ($argumentType === 'string'){
            $stringPattern = '/^(?:[^#\\\\]|\\\\[0-9]{3})*$/';
            if (!preg_match($stringPattern, $argumentValue)){
                throw new StudentException(32);
            }        
        }
        else if ($argumentType === 'nil'){
            if (strtolower($argumentValue) !== 'nil'){
                throw new StudentException(32);
            }
        }
        else if ($argumentType === 'bool'){
            if (strtolower($argumentValue) !== 'true' &&
            strtolower($argumentValue) !== 'false'){
                throw new StudentException(32);
            }
        }
    }
}