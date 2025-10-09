<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request has Authorization header with Bearer token
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            Log::warning('API request without Bearer token', [
                'url' => $request->url(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'Missing or invalid authorization header'
            ], 401);
        }

        // Extract token from header
        $token = substr($authHeader, 7);
        
        // Validate token using Sanctum
        try {
            // Set the Authorization header for Sanctum to process
            $request->headers->set('Authorization', $authHeader);

            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                Log::warning('Invalid token used', [
                    'token_prefix' => substr($token, 0, 10) . '...',
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token',
                    'error' => 'Token authentication failed'
                ], 401);
            }

            // Log successful authentication
            Log::info('API request authenticated', [
                'user_id' => $user->id_karyawan,
                'url' => $request->url(),
                'method' => $request->method(),
            ]);

            // Set the authenticated user on the request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            // Also add user to request for easy access in controllers
            $request->merge(['authenticated_user' => $user]);
            
        } catch (\Exception $e) {
            Log::error('Authentication middleware error', [
                'error' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication error',
                'error' => 'Internal authentication error'
            ], 500);
        }

        return $next($request);
    }
}
