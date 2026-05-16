<?php
namespace App\Exceptions;
use Exception;

class ClientHasActiveOrdersException extends Exception
{
    public function __construct(string $message = 'El cliente tiene ordenes de trabajo activas.')
    {
        parent::__construct($message, 409);
    }

    public function render()
    {
        return response()->json(['message' => $this->getMessage()], 409);
    }
}
