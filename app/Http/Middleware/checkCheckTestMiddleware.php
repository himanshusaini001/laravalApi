<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkCheckTestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    { 
      
       // return $next($request);
        $customKey = env('NUM_KEY');
        // Retrieve the query parameters from the request
        $queryParams = $request->all();
        
        // Check if the "test" parameter exists and its value is "yes"
        if ($request->headers->has('custom-key') && $request->headers->get('custom-key') === $customKey) {
            return $next($request);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'dose not exist Key',
            ]);
        }
        
    }
}
