<?php

namespace App\Traits;

trait DownloadKey
{
    protected function userKey($userId)
    {
        return 'user_' . $userId;
    }

    protected function fileDownloadKey()
    {
        return 'daily_file_download';
    }
    protected function freeFileDownloadKey()
    {
        return 'daily_free_file_download';
    }
}
