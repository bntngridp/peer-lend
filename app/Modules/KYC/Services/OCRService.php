<?php

namespace App\Modules\KYC\Services;

use Illuminate\Http\UploadedFile;

class OCRService
{
    /**
     * Simulate OCR scanning of a KTP document card.
     */
    public function parseKTP(UploadedFile $file, string $profileName): array
    {
        // 1. Simulate failure/mismatch if the filename indicates a bad scan
        if (str_contains(strtolower($file->getClientOriginalName()), 'blurry')) {
            return [
                'nik'       => '0000000000000000',
                'full_name' => 'WRONG NAME OCR',
            ];
        }

        // 2. Generate a random 16-digit Indonesian NIK
        $nikPrefix = '3174'; // Jakarta Selatan prefix example
        $nikSuffix = '';
        for ($i = 0; $i < 12; $i++) {
            $nikSuffix .= random_int(0, 9);
        }
        $nik = $nikPrefix . $nikSuffix;

        // 3. OCR extraction normally yields uppercase names
        $extractedName = strtoupper(trim($profileName));

        return [
            'nik'       => $nik,
            'full_name' => $extractedName,
        ];
    }
}
