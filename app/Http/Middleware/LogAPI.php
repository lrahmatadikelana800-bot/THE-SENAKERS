<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\LogModel;
use App\Helpers\ApiFormatter;
use Throwable;

class LogAPI
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;
        
        // Cek user dari JWT token
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            $user = null;
        }
        
        // Filter data sensitif sebelum logging
        $filteredRequest = ApiFormatter::filterSensitiveData($request->all());
        
        // Buat log entry
        $log = LogModel::create([
            'user_id' => $user ? $user->id : null,
            'log_method' => $request->method(),
            'log_url' => $request->fullUrl(),
            'log_ip' => $request->ip(),
            'log_request' => json_encode($filteredRequest),
        ]);
        
        try {
            // Lanjutkan request
            $response = $next($request);
            
            // Update log dengan response
            $log->update([
                'log_response' => $response->getContent(),
            ]);
            
            return $response;
            
        } catch (Throwable $e) {
            // Handle error
            $errorResponse = ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
            
            $log->update([
                'log_response' => json_encode($errorResponse),
            ]);
            
            return response()->json($errorResponse, 500);
        }
    }
}