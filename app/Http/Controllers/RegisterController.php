<?php

namespace App\Http\Controllers;

use App\Http\Requests\HandleRegisterRequest;
use App\Traits\HasUsername;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendVerificationCodeJob;
use App\Http\Requests\VerifyRegisterRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Traits\HasToken;
use App\Http\Requests\ResendRegisterRequest;

class RegisterController extends Controller
{
    use HasUsername;
    use HasToken;
    
    public function handle(HandleRegisterRequest $request)
    {
        /**
         * @post('/api/auth/register')
         * @name('auth.user.register')
         * @middlewares('api', 'guest', 'throttle:20')
         */
        $email=$request->input('email');
        $cacheData=$this->getCacheData($email);
        
        $cacheData['try']=isset($cacheData['try']) ? $cacheData['try']+1 :1;
        $cacheData['last_try']=now();
        $cacheData['code']=self::generateOTPCode();
        $cacheData['data']=$request->safe()->all();
    
        Cache::put(self::getCacheKey($email), $cacheData, now()->addMinutes(20));
        
        $this->sendVerificationCode($email, $cacheData['code']);
        
        return apiResponse()->status(201)->content($cacheData['data'])->success();
    }
    
    public function checkUserAlreadyExists($email)
    {
        return User::findByEmail($email);
    }

    
    public function getCacheData($email)
    {
        $key=self::getCacheKey($email);
        return Cache::get($key, []);
    }

    
    private static function getCacheKey($key)
    {
        return "user.register.{$key}";
    }

    private function generateOTPCode()
    {
        return rand(0, 9). rand(10, 99). rand(10, 99);
    }
    
    
    public function sendVerificationCode($email, $code)
    {
        return SendVerificationCodeJob::dispatchSync($email, $code);
    }


    public function verify(VerifyRegisterRequest $request)
    {
        /**
         * @post('/api/auth/verify')
         * @name('auth.user.verify')
         * @middlewares('api', 'guest', 'throttle:20')
         */
        $email = $request->input('email');

        $cacheData = self::getCacheData($email);

        $data = Arr::except($cacheData['data'], ['password_confirmation']);

        $data['password'] = Hash::make($data['password']);

        $data['email_verified_at'] = now();

        $user = User::query()->create($data);

        Cache::forget(self::getCacheKey($email));

        $tokenString = $this->getToken($user, $request->input('device_name'));

        $tokenData = $this->tokenData($tokenString, $user);

        return apiResponse()->content($tokenData)->success();
    }
    public function resend(ResendRegisterRequest $request)
    {
        /**
         * @post('/api/auth/resend')
         * @name('auth.user.resend')
         * @middlewares('api', 'guest', 'throttle:20')
         */
        $cacheData = self::getCacheData($request->input('email'));

        $cacheData['last_try'] = now();
        $cacheData['try'] = $cacheData['try'] + 1;
        $cacheData['code'] = self::generateOTPCode();

        Cache::put(self::getCacheKey($request->input('email')), $cacheData, now()->addMinutes(20));
        $this->sendVerificationCode($request->input('email'), $cacheData['code']);

        return apiResponse()->status(201)->content([
            'status'=>'SUCCESS'
        ])->success();
    }
    private function getToken($user, string $deviceName): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }
}
