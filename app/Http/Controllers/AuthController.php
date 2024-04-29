<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller implements HasMiddleware
{
    
    public static function middleware(): array {
        return [ new Middleware('auth:api', only: ['logout']) ];
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
            return response(['error' => $e->getMessage()]);
        }

        return response($refreshToken);

    }

    public function logout() {
        return auth()->logout();
    }
}