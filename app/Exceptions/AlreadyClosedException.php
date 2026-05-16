<?php
namespace App\Exceptions;
use Exception;

class AlreadyClosedException extends Exception
{
    public function __construct(string $message = 'La orden de trabajo ya fue cerrada.')
    {
        parent::__construct($message, 409);
    }

    public function render()
    {
        return response()->json(['message' => $this->getMessage()], 409);
    }
}
