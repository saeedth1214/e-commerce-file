<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use App\Http\Requests\UpdateProfileDetailRequest;
use App\Http\Requests\UpdateProfileAvatarRequest;
use App\Http\Requests\UpdateProfilePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ProfileController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }
    public function show()
    {
        /**
         * @get('/api/user/profile')
         * @name('profile.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return fractal()
            ->item($this->user)
            ->withResourceName('users')
            ->transformWith(UserTransformer::class)
            ->respond();
    }

    public function update(UpdateProfileDetailRequest $request)
    {
        /**
         * @put('/api/user/profile')
         * @name('profile.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $profileData = $request->safe()->all();
        $this->user->update($profileData);
        return apiResponse()->empty();
    }

    public function changeAvatar(UpdateProfileAvatarRequest $request)
    {
        /**
         * @post('/api/user/profile/change-avatar')
         * @name('profile.avatar')
         * @middlewares('api', 'auth:sanctum')
         */
        try {
            $this->user->addMediaFromRequest('file')
                ->toMediaCollection('avatar-image');
        } catch (FileDoesNotExist $exception) {
            return apiResponse()
                ->status(400)
                ->message('File does not exists.')
                ->fail();
        } catch (FileIsTooBig $exception) {
            return apiResponse()
                ->status(400)
                ->message('File is too big.')
                ->fail();
        }

        return apiResponse()->empty();
    }


    public function changePassword(UpdateProfilePasswordRequest $request)
    {
        /**
         * @post('/api/user/profile/change-password')
         * @name('profile.password')
         * @middlewares('api', 'auth:sanctum')
         */
        $password = Hash::make($request->input('newPassword'));

        $this->user->update(compact('password'));

        return apiResponse()->empty();
    }
}
