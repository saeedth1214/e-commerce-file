<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\AmountAfterModelRebate;
use Closure;
use Illuminate\Http\Request;

class EnsureUserCanDownloadFile
{
    use AmountAfterModelRebate;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $count = User::query()->userHasThisFile($request->file->id);
        // the user have bought this file .
        if ($count) {
            return $next($request);
        }

        // check the user have got a plan.
        /**
         * @var User $user
         */
        $user = auth()->user();
        $active_plan = $user->activePlan();
        if ($active_plan) {
            return $next($request);
        }

        return apiResponse()->status(403)->fail();
    }
}
