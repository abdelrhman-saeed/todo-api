<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\GuestApi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller implements HasMiddleware
{
    
    public static function middleware(): array {
        return [
            new Middleware(AuthenticateApi::class, except: ['login']),
            new Middleware(GuestApi::class, only: ['login']),
        ];
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate(
            ['email' => 'required|email', 'password' => 'required']
        );

        return ( $token = auth()->attempt($credentials) )
            ? response($token)
            : response(['error' => 'wrong credentials'], 401);          
    }

    public function refresh() {


        try {
            // checking if token is blacklisted
            $refreshToken = auth()->refresh(true, true);
        }
        catch(TokenInvalidException $e) {
            return response(['error' => $e->getMessage()], 401);
        }

        return response($refreshToken);

    }

    public function logout() {
        auth()->logout(true);
    }
}
