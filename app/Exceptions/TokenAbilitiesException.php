<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class TokenAbilitiesException extends Exception
{
    /**
     * Create a new TokenAbilitiesException instance.
     *
     * This exception is thrown when a token does not have the required abilities
     * to perform a certain action, resulting in an unauthorized access attempt.
     *
     * @param  string|null  $message  The Exception message to throw.
     * @param  int  $code  The Exception code.
     * @param  Throwable|null  $previous  The previous throwable used for exception chaining.
     */
    public function __construct(?string $message = null, int $code = 403, ?Throwable $previous = null)
    {
        parent::__construct($message ?? 'Unauthorized Action.', 403, $previous);
        $this->code = $code ?: 403;
    }
}
