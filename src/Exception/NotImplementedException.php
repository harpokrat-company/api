<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class NotImplementedException extends HttpException
{
    public function __construct(Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        parent::__construct(501, 'not implemented', $previous, $headers, $code);
    }
}
