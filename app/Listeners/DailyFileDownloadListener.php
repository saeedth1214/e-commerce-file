<?php

namespace App\Listeners;

use App\Traits\AmountAfterModelRebate;
use App\Traits\DownloadKey;
use Illuminate\Support\Facades\Cache;

class DailyFileDownloadListener
{

    use DownloadKey, AmountAfterModelRebate;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->file->sale_as_single) {
            return;
        }
        /**
         * @var User $user
         */
        $user = auth()->user();
        $active_plan = $user->activePlan();
        $userKey = $this->userKey($user->id);
        $amount = $this->calculateRebate($event->file);
        if ((int)$amount === 0) {
            $downloadKey = $this->freeFileDownloadKey();
            $daily_free_download_data = Cache::get($downloadKey, []);
            if (!isset($daily_free_download_data[$userKey])) {
                $daily_free_download_data[$userKey] = 1;
            } else {
                if ($daily_free_download_data[$userKey] + 1 > $active_plan->daily_free_download_limit_count) {
                    apiResponse()
                        ->status(403)
                        ->message('تعداد دانلود روزانه شما بیشتر از حد مجاز است. لطفا تا پایان روز صبر کنید.')
                        ->fail()
                        ->throwResponse();
                } else {
                    $daily_free_download_data[$userKey] += 1;
                }
            }
            Cache::put($downloadKey, $daily_free_download_data, now()->endOfDay());
        } else {
            $downloadKey = $this->fileDownloadKey();
            $daily_download_data = Cache::get($downloadKey, []);
            if (!isset($daily_free_download_data[$userKey])) {
                $daily_download_data[$userKey] = 1;
            } else {
                if ($daily_download_data[$userKey] + 1 > $active_plan->daily_download_limit_count) {
                    apiResponse()
                        ->status(403)
                        ->message('تعداد دانلود روزانه شما بیشتر از حد مجاز است. لطفا تا پایان روز صبر کنید.')
                        ->fail()
                        ->throwResponse();
                } else {
                    $daily_download_data[$userKey] += 1;
                }
            }
            Cache::put($downloadKey, $daily_download_data, now()->endOfDay());
        }
    }
}
