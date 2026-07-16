<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rate limit: 3 submissions per 10 minutes per IP for public forms
        RateLimiter::for('public-submission', function (Request $request) {
            return Limit::perMinutes(10, 3)
                ->by($request->ip())
                ->response(function () use ($request) {
                    // Log ke database saat melebihi batas rate limit
                    try {
                        \App\Models\SecurityLog::create([
                            'event_type' => 'rate_limit_exceeded',
                            'description' => 'User (atau Bot) mengirim form terlalu sering melebihi batas wajar (Rate Limiter).',
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'endpoint' => $request->fullUrl(),
                            'payload' => $request->except(['file_berkas_aduan', 'file_permohonan_audiensi']), // Jangan log file mentah
                        ]);
                    } catch (\Exception $e) {
                        // ignore
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Anda telah mencapai batas pengiriman. Silakan coba lagi dalam 10 menit.',
                    ], 429);
                });
        });

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
    }
}
