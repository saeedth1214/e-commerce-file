<?php

namespace App\Traits;

use Hekmatinasser\Verta\Facades\Verta;

trait ConvertDateTime
{
    protected function shamsiDate(?string $dateTime)
    {
        if (!$dateTime) {
            return;
        }
        return Verta::instance($dateTime)->format('Y-n-j H:i:s');
    }

    protected function formatDiffrence(?string $dateTime)
    {
        if (!$dateTime) {
            return;
        }
        return Verta::instance($dateTime)->formatDifference();
    }
}
