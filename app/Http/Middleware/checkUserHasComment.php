<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkUserHasComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $gurad)
    {
        $user = auth($gurad)->user();
        $comment = $user->comments()->where('id', $request->input('comment'))->first();
        if ($comment) {
            return $next($request);
        }

        return apiResponse()->status(403)->fail();
    }
}
