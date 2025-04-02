<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidRelationshipException extends Exception
{
    public function __construct(?string $message = null, int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message ?? 'Invalid Relationship', 422, $previous);
        $this->code = $code ?: 422;
    }
}
