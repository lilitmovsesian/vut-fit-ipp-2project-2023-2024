<?php

namespace IPP\Student;

use IPP\Core\Exception\IPPException;
use Throwable;

/** 
* Represents exceptions specific to interpret errors.
*/
class StudentException extends IPPException 
{
    /**
     * StudentException constructor.
     * @param int $code The error code.
     * @param Throwable|null $previous The previous exception.
     */
    public function __construct(int $code, ?Throwable $previous = null)
    {
        $message = self::getErrorMessage($code);
        parent::__construct($message, $code, $previous, false);
    }

     /**
     * Gets the error message based on the error code.
     * @param int $code The error code.
     * @return string The error message.
     */
    private static function getErrorMessage(int $code): string
    {
        switch ($code) {
            case 31:
                return "Incorrect XML format in the input file (file is not well-formed).";
            case 32:
                return "Unexpected XML structure (e.g., element for argument outside instruction element, instruction with duplicate or negative order).";
            case 88:
                return "Integration error (invalid integration with the ipp-core framework).";
            case 52:
                return "Semantic errors in the input code in IPPcode24 (e.g., using an undefined label, variable redefinition).";
            case 53:
                return "Interpretation runtime error - incorrect operand types.";
            case 54:
                return "Interpretation runtime error - accessing a non-existent variable (memory frame exists).";
            case 55:
                return "Interpretation runtime error - memory frame does not exist (e.g., reading from an empty frame stack).";
            case 56:
                return "Interpretation runtime error - missing value (in a variable, on the data stack, or in the call stack).";
            case 57:
                return "Interpretation runtime error - incorrect operand value (e.g., division by zero, incorrect return value of the EXIT instruction).";
            case 58:
                return "Interpretation runtime error - incorrect string manipulation.";
            default:
                return "Error.";
        }
    }
}
