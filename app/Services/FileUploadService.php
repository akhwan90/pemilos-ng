<?php

namespace App\Services;

use App\Models\SecurityLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    private array $documentMimes = ['application/pdf'];
    private array $imageMimes = ['image/jpeg', 'image/jpg', 'image/png'];
    private int $maxFileSize = 5120; // 5MB in KB

    public function uploadDocument(UploadedFile $file, string $prefix = 'doc'): string
    {
        $this->validateFile($file, $this->documentMimes);
        return $this->storeFile($file, $prefix);
    }

    public function uploadImage(UploadedFile $file, string $prefix = 'img'): string
    {
        $this->validateFile($file, $this->imageMimes);
        return $this->storeFile($file, $prefix);
    }

    public function uploadMixed(UploadedFile $file, string $prefix = 'berkas'): string
    {
        $allowedMimes = array_merge($this->documentMimes, $this->imageMimes);
        $this->validateFile($file, $allowedMimes);
        return $this->storeFile($file, $prefix);
    }

    private function validateFile(UploadedFile $file, array $allowedMimes): void
    {
        if (!$file->isValid()) {
            abort(422, 'File tidak valid.');
        }

        if ($file->getSize() > $this->maxFileSize * 1024) {
            abort(422, 'Ukuran file maksimal 5MB.');
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, $allowedMimes)) {
            $allowed = implode(', ', $allowedMimes);
            abort(422, "Tipe file tidak diizinkan. Hanya $allowed yang diperbolehkan.");
        }

        // --- PENAMBAHAN KEAMANAN PENTEST ---
        
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = $file->getClientOriginalName();
        $content = file_get_contents($file->getRealPath());

        // 1. Ekstensi ganda nakal
        if (preg_match('/\.(php|phtml|php3|php4|php5|php7|php8|phar|inc|sh|cgi|pl|py|exe|sh)\./i', $fileName)) {
            $this->logSecurityEvent('malicious_file_upload', 'Format penamaan file terdeteksi tidak aman (Double Extension)', $fileName, $mime);
            abort(422, 'Format penamaan file terdeteksi tidak aman.');
        }

        // 2. Jika ini gambar, periksa apakah ada script tersembunyi (exif injection / shell backdoor)
        if (str_starts_with($mime, 'image/')) {
            if (preg_match('/<\?php/i', $content) || preg_match('/<\?\s/i', $content) || preg_match('/<\?=/i', $content)) {
                $this->logSecurityEvent('malicious_file_upload', 'Terdeteksi malicious payload (PHP Script) di dalam file gambar', $fileName, $mime);
                abort(422, 'Terdeteksi malicious payload (script berbahaya) di dalam file gambar.');
            }
        }

        // 3. Jika ini PDF, periksa anomali Javascript
        if ($mime === 'application/pdf') {
            if (strpos($content, '%PDF-') !== 0) {
                $this->logSecurityEvent('malicious_file_upload', 'File PDF palsu (Struktur header tidak valid)', $fileName, $mime);
                abort(422, 'File ini terdeteksi bukan struktur PDF yang valid.');
            }
            if (preg_match('/\/JS\s/i', $content) || preg_match('/\/JavaScript\s/i', $content)) {
                $this->logSecurityEvent('malicious_file_upload', 'File PDF mengandung injeksi JavaScript', $fileName, $mime);
                abort(422, 'File PDF mengandung script berbahaya yang tidak diizinkan.');
            }
        }
    }

    private function logSecurityEvent(string $type, string $description, string $fileName, string $mime): void
    {
        try {
            $request = request();
            SecurityLog::create([
                'event_type' => $type,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->fullUrl(),
                'payload' => [
                    'file_name' => $fileName,
                    'mime_type' => $mime,
                ],
                'user_id' => auth('sanctum')->id(), // Mencatat admin ID jika sedang login
            ]);
        } catch (\Exception $e) {
            // Abaikan jika logging gagal, agar tidak memutus flow abort() utama
        }
    }

    private function storeFile(UploadedFile $file, string $prefix): string
    {
        $extension = $file->getClientOriginalExtension();
        $randomName = sprintf(
            '%s_%s.%s',
            $prefix,
            Str::random(16),
            $extension
        );

        $path = $file->storeAs('uploads', $randomName, 'public');

        if (!$path) {
            abort(500, 'Gagal menyimpan file.');
        }

        return $path;
    }
}
