<?php
namespace App\Exceptions;
use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $message = 'Stock insuficiente para completar la operacion.')
    {
        parent::__construct($message, 422);
    }

    public function render()
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
