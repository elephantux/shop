<?php

namespace App\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;

class FakerRandomImageProvider extends Base
{
    public function getRandomImage(string $fromPath, string $toPath): string
    {
        $storagePath = 'public/' . $toPath;

        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        $path = $this->generator->file(
            base_path($fromPath),
            Storage::path($storagePath),
            false
        );

        return "$toPath/$path";
    }
}
