<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AppException extends HttpException implements AppExceptionInterface {
    
}