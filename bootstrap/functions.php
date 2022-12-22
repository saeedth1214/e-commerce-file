<?php
/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

if (! function_exists('apiResponse')) {
    function apiResponse()
    {
        return new App\Services\Response();
    }
}
