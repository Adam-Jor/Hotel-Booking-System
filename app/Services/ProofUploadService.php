<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProofUploadService
{
    /**
     * Allowed MIME types for payment proofs.
     */
    protected const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];

    /**
     * Magic bytes signatures for file type verification.
     */
    protected const MAGIC_BYTES = [
        'jpg'  => [0xFF, 0xD8, 0xFF],
        'png'  => [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A],
        'pdf'  => [0x25, 0x50, 0x44, 0x46], // %PDF
    ];

    /**
     * Maximum file size in kilobytes.
     */
    protected const MAX_SIZE_KB = 2048;

    /**
     * Store payment proof with security validation.
     *
     * @throws \RuntimeException if file is invalid or dangerous
     */
    public function store(UploadedFile $file, string $folder = 'payments'): string
    {
        $this->validateFile($file);

        $directory = storage_path("app/public/{$folder}");
        File::ensureDirectoryExists($directory);

        $extension = $this->getValidatedExtension($file);
        $filename = now()->format('YmdHis').'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$extension;

        // Store in storage/app/public (not public/) for better security
        $path = $file->storeAs($folder, $filename, 'public');

        // Set secure permissions
        chmod(storage_path("app/public/{$path}"), 0640);

        return $path; // e.g., "payments/20261126123456-proof.jpg"
    }

    /**
     * Validate uploaded file thoroughly.
     */
    protected function validateFile(UploadedFile $file): void
    {
        // 1. Check upload error
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('File upload failed with error code: '.$file->getError());
        }

        // 2. Check file size
        if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
            throw new \RuntimeException('File size exceeds '.self::MAX_SIZE_KB.'KB limit.');
        }

        // 3. Check MIME type against allowed list
        $mime = $file->getMimeType();
        if (! isset(self::ALLOWED_MIMES[$mime])) {
            throw new \RuntimeException('File type not allowed. Allowed: JPG, PNG, PDF.');
        }

        // 4. Verify file content matches declared type (magic bytes)
        $this->verifyMagicBytes($file, $mime);

        // 5. Check for malicious content in images
        if (str_starts_with($mime, 'image/')) {
            $this->verifyImageSafety($file);
        }

        // 6. Check PDF safety
        if ($mime === 'application/pdf') {
            $this->verifyPdfSafety($file);
        }
    }

    /**
     * Verify file magic bytes match expected type.
     */
    protected function verifyMagicBytes(UploadedFile $file, string $mime): void
    {
        $expectedExtension = self::ALLOWED_MIMES[$mime];
        $expectedBytes = self::MAGIC_BYTES[$expectedExtension];

        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, max(8, count($expectedBytes)));
        fclose($handle);

        $bytes = array_values(unpack('C*', $header));

        foreach ($expectedBytes as $index => $expectedByte) {
            if (! isset($bytes[$index]) || $bytes[$index] !== $expectedByte) {
                throw new \RuntimeException('File content does not match declared type. Possible spoofing attempt.');
            }
        }
    }

    /**
     * Basic image safety checks.
     */
    protected function verifyImageSafety(UploadedFile $file): void
    {
        try {
            $info = getimagesize($file->getRealPath());
            if ($info === false) {
                throw new \RuntimeException('Invalid image file.');
            }

            // Check dimensions aren't absurd (potential decompression bomb)
            [$width, $height] = $info;
            if ($width > 5000 || $height > 5000) {
                throw new \RuntimeException('Image dimensions too large.');
            }

            // Check for PHP code in image metadata
            $exif = @exif_read_data($file->getRealPath());
            if ($exif) {
                foreach ($exif as $section) {
                    if (is_array($section)) {
                        foreach ($section as $value) {
                            if (is_string($value) && preg_match('/<\?php|eval\(|system\(|exec\(/i', $value)) {
                                throw new \RuntimeException('Suspicious content detected in image metadata.');
                            }
                        }
                    }
                }
            }
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Throwable) {
            throw new \RuntimeException('Image verification failed.');
        }
    }

    /**
     * Basic PDF safety checks.
     */
    protected function verifyPdfSafety(UploadedFile $file): void
    {
        $content = file_get_contents($file->getRealPath());

        // Check for JavaScript/embedded files/launch actions
        $dangerousPatterns = [
            '/\/JavaScript\s/',
            '/\/JS\s/',
            '/\/Launch\s/',
            '/\/EmbeddedFile\s/',
            '/\/RichMedia\s/',
            '/\/XFA\s/',
            '/\/ObjStm\s/',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new \RuntimeException('PDF contains potentially dangerous features (JavaScript, embedded files, etc.).');
            }
        }

        // Limit pages (prevent DoS via huge PDF)
        if (preg_match_all('/\/Type\s*\/Pages\b/', $content) > 50) {
            throw new \RuntimeException('PDF has too many pages (max 50).');
        }
    }

    /**
     * Get validated extension from MIME type.
     */
    protected function getValidatedExtension(UploadedFile $file): string
    {
        $mime = $file->getMimeType();
        return self::ALLOWED_MIMES[$mime] ?? 'bin';
    }

    /**
     * Get public URL for stored proof.
     */
    public function getPublicUrl(string $path): string
    {
        return asset('storage/'.$path);
    }
}