<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class ImageService
{
    public function store(UploadedFile $file, string $folder): string
    {
        $directory = public_path("uploads/{$folder}");
        File::ensureDirectoryExists($directory);

        $name = now()->format('YmdHis').'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $name.'.webp';

        (new ImageManager(new GdDriver()))
            ->decodePath($file->getRealPath())
            ->scaleDown(width: 1400)
            ->save($directory.DIRECTORY_SEPARATOR.$filename, quality: 80);

        return "uploads/{$folder}/{$filename}";
    }

    /**
     * Proofs may be PDFs, so skip the image pipeline for those.
     */
    public function storeProof(UploadedFile $file, string $folder = 'payments'): string
    {
        if ($file->getMimeType() === 'application/pdf') {
            $directory = public_path("uploads/{$folder}");
            File::ensureDirectoryExists($directory);

            $filename = now()->format('YmdHis').'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.pdf';
            $file->move($directory, $filename);

            return "uploads/{$folder}/{$filename}";
        }

        return $this->store($file, $folder);
    }
}
