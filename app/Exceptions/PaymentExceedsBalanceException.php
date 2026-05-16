<?php
namespace App\Exceptions;
use Exception;

class PaymentExceedsBalanceException extends Exception
{
    public function __construct(string $message = 'El pago supera el monto pendiente.')
    {
        parent::__construct($message, 422);
    }

    public function render()
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
