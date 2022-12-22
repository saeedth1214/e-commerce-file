<?php

namespace App\Http\Controllers;


use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Jobs\SendForgetPasswordTokenJob;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        /**
         * @post('/api/auth/forget-password')
         * @name('auth.user.forget-password')
         * @middlewares('api', 'guest', 'throttle:20')
         */
        $email=$request->input('email');
    
        $token=$this->generateToken();
        $cacheData=$this->getCacheData($email);
        $cacheData['try'] = isset($cacheData['try']) ? $cacheData['try'] + 1 : 1;
        $cacheData['last_try'] = now();
        $cacheData['token'] = $token;
        
        Cache::put(self::getCacheKey($email), $cacheData, now()->addMinutes(20));
        
        $this->sendForgetPasswordEmail($email, $token);
        
        return apiResponse()->content([
            'status'=>'SUCCESS',
        ])->success();
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        /**
         * @post('/api/auth/change-password')
         * @name('auth.user.change-password')
         * @middlewares('api', 'guest', 'throttle:20')
         */
        $password=$request->input('password');
        
        $user=User::findByEmail($request->input('email'));
        
        $user->update(compact('password'));
        
        Cache::forget($this->getCacheKey($request->input('email')));
        
        return apiResponse()->content([
            'status'=>'SUCCESS'
        ])->success();
    }
    
    
    
    public function sendForgetPasswordEmail($email,$token)
    {
        return SendForgetPasswordTokenJob::dispatch($email, $token);
    }
    
    
    public function generateToken()
    {
        return Str::random(24);
    }
    
    public function getCacheKey($email)
    {
        return "user.forget-password.${email}";
    }
    
    public function getCacheData($email)
    {
        $key=$this->getCacheKey($email);
        return Cache::get($key, []);
    }
}
