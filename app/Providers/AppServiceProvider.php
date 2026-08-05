<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rate limit untuk Login: 5 percobaan per menit per IP
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () use ($request) {
                    try {
                        \App\Models\SecurityLog::create([
                            'event_type' => 'brute_force_login',
                            'description' => 'Terdeteksi upaya Bruteforce Login (melebihi 5 percobaan per menit).',
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'endpoint' => $request->fullUrl(),
                            'payload' => ['username_attempt' => $request->input('username')],
                        ]);
                    } catch (\Exception $e) {
                        // ignore
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak percobaan login. Silakan tunggu 1 menit.',
                    ], 429);
                });
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
