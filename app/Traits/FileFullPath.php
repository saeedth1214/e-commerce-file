<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Str;

trait FileFullPath
{
    protected function ResolveFileFullPath(File $file)
    {
        $file_name = $file->getMedia('file-image')[0]->file_name;

        $explode_file_name = explode('.', $file_name);

        $format = $file->format();

        $explode_file_name[count($explode_file_name) - 1] = $format;
        
        return implode('.', $explode_file_name);
    }
}
