<?php

namespace App\Exception;

class BadRequestHttpException extends AppException
{
    public function __construct(string $message, \Throwable $exception = null, int $code = 0, array $headers = [])
    {
        parent::__construct(400, $message, $exception, $headers, $code);
    }
}