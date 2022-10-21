<?php

namespace App\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;

class FakerRandomImageProvider extends Base
{
    public function getRandomImage(string $fromPath, string $toPath): string
    {
        if (!Storage::exists($toPath)) {
            Storage::makeDirectory($toPath);
        }

        $path = $this->generator->file(
            base_path($fromPath),
            Storage::path($toPath),
            false
        );

        return "/storage/app/$toPath/$path";
    }
}
