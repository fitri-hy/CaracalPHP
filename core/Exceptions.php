<?php
namespace Caracal\Core;

use Throwable;

class CaracalException extends \Exception
{
    protected array $context = [];

    public function __construct(string $message = "", int $code = 0, array $context = [], Throwable $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code'    => $this->getCode(),
            'file'    => $this->getFile(),
            'line'    => $this->getLine(),
            'context' => $this->context,
            'trace'   => $this->getTraceAsString(),
        ];
    }
}

class NotFoundException extends CaracalException {}
class ValidationException extends CaracalException {}
class CSRFException extends CaracalException {}