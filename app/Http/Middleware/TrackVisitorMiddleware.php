<?php

namespace App\Http\Middleware;

use App\Services\VisitorAnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorMiddleware
{
    public function __construct(
        protected VisitorAnalyticsService $analyticsService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Record public page view only for successful GET requests
        if ($request->isMethod('GET') && in_array($response->getStatusCode(), [200, 304])) {
            try {
                $this->analyticsService->recordPageView($request);
            } catch (\Throwable $e) {
                // Tracking failure must never break public request delivery
                report($e);
            }
        }

        return $response;
    }
}
