<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk OWASP Security Headers compliance
 * 
 * Menerapkan security headers berikut:
 * - X-Content-Type-Options: nosniff (Mencegah MIME sniffing)
 * - X-Frame-Options: DENY (Mencegah Clickjacking)
 * - X-XSS-Protection: 1; mode=block (Mencegah XSS di browser lama)
 * - Referrer-Policy: strict-origin-when-cross-origin
 * - Permissions-Policy (Membatasi fitur browser)
 * - Strict-Transport-Security (HSTS - untuk production via HTTPS)
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // OWASP Recommended Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 
            'camera=(), geolocation=(), microphone=(), payment=(), usb=()'
        );

        // HSTS - hanya aktif jika request via HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Cache-Control untuk API responses (jangan cache data sensitif)
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');

        return $response;
    }
}