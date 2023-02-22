<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ViewerCounter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $file = $request->file;
        $categoryName = $file->category->name;
        $key= $file->id;
        Redis::hSetNx($key,'title',$file->title);
        Redis::hSetNx($key,'category_name',$categoryName);
        Redis::hSetNx($key,'id',$file->id);
        Redis::hINCRBY($key,'views',1);
     
        $views = Redis::hGet($key,'views');

        Redis::zAdd('view-counter',$views,$key);
        return $next($request);
    }
}
