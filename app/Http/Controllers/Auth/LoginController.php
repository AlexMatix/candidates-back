<?php

namespace App\Http\Controllers\Auth;

use App\Traits\ApiResponse;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends AccessTokenController
{
    use ApiResponse;

    public function issueToken(ServerRequestInterface $request)
    {
        return self::issueToken($request);
    }
}
