<?php

namespace App\Http\Controllers;

use App\Http\Requests\HandleLoginRequest;
use App\Models\User;
use App\Traits\HasToken;
use App\Traits\HasUsername;
use Illuminate\Support\Facades\Hash;
use App\Transformers\UserTransformer;

class LoginController extends Controller
{
    use HasToken;
    use HasUsername;

    public function handle(HandleLoginRequest $request)
    {
        /**
         * @post('/api/auth/login')
         * @name('auth.user.login.handle')
         * @middlewares('api', 'guest', 'throttle:20')
         */
        $user = $this->getUser($request);

        $credential = $this->badCredential($user, $request->input('password'));

        if ($credential) {
            return apiResponse()
                ->message('Your credentials are incorrect.')
                ->fail();
        }
        $tokenString = $this->getToken($user, $request->input('device_name'));
        $tokenData = $this->tokenData($tokenString, $user);

        return apiResponse()->content($tokenData)->success();
    }

    private function getUser(HandleLoginRequest $request)
    {
        $type = $this->getUsernameType($request->input('username'));

        return User::findByUserNameType($type, $request->input('username'));
    }

    private function badCredential($user, $password)
    {
        return !Hash::check($password, optional($user)->password);
    }


    private function getToken($user, $deviceName)
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    public function current_user()
    {
        /**
         * @get('/api/auth/user')
         * @name('auth.user.current_user')
         * @middlewares('api', 'auth:sanctum')
         */
        $user = auth()->user();

        return fractal()
            ->item($user)
            ->withResourceName('users')
            ->transformWith(UserTransformer::class)
            ->respond();
    }
}
