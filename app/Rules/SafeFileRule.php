<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeFileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            $fail('File tidak valid.');
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $mime = $value->getMimeType();

        // 1. Ekstensi file nakal ganda (misal: shell.php.jpg)
        $fileName = $value->getClientOriginalName();
        if (preg_match('/\.(php|phtml|php3|php4|php5|php7|php8|phar|inc|sh|cgi|pl|py|exe|sh)\./i', $fileName)) {
            $fail('Format penamaan file terdeteksi tidak aman.');
            return;
        }

        // 2. Jika file ini PDF, pastikan benar-benar berstruktur PDF
        if ($extension === 'pdf' || $mime === 'application/pdf') {
            $content = file_get_contents($value->getRealPath());
            
            // Header PDF harus berawalan %PDF-
            if (strpos($content, '%PDF-') !== 0) {
                $fail('File ini bukan file PDF yang valid.');
                return;
            }
            
            // Cek anomali injeksi script JavaScript ke dalam PDF
            if (preg_match('/\/JS\s/i', $content) || preg_match('/\/JavaScript\s/i', $content)) {
                $fail('File PDF mengandung script berbahaya yang tidak diizinkan.');
                return;
            }
        }

        // 3. Jika file ini adalah Gambar, periksa injeksi script di baliknya
        if (in_array($extension, ['jpg', 'jpeg', 'png']) || str_starts_with($mime, 'image/')) {
            $content = file_get_contents($value->getRealPath());
            
            // Deteksi tag pembuka script PHP (<?php atau <?) di dalam metadata gambar (Exif injection)
            if (preg_match('/<\?php/i', $content) || preg_match('/<\?\s/i', $content) || preg_match('/<\?=/i', $content)) {
                $fail('Terdeteksi script berbahaya (Malicious Payload) tersembunyi di dalam file gambar.');
                return;
            }
        }
    }
}
