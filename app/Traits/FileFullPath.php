<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Str;

trait FileFullPath
{
    protected function ResolveFileFullPath(File $file)
    {
        $file_name = $file->getMedia('file-image')[0]->name;

        $format = $file->format();

        $fullPath = $file_name . '.' . $format;

        return $fullPath;
    }
}
