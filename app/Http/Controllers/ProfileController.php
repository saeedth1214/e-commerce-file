<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use App\Http\Requests\UpdateProfileDetailRequest;
use App\Http\Requests\UpdateProfileAvatarRequest;
use App\Http\Requests\UpdateProfilePasswordRequest;
use App\Transformers\FileTransformer;
use App\Transformers\OrderTransformer;
use App\Transformers\PlanTransformer;
use Illuminate\Support\Facades\Hash;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ProfileController extends Controller
{
    /**
     * @var User $user
     */
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

    public function plans()
    {

        $plans = $this->user->plans()->paginate();

        return fractal()
            ->collection($plans)
            ->withResourceName('plans')
            ->transformWith(PlanTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($plans))
            ->respond();
    }
    public function files()
    {

        $files = $this->user->files()->paginate();

        return fractal()
            ->collection($files)
            ->withResourceName('files')
            ->transformWith(FileTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($files))
            ->respond();
    }
    public function orders()
    {

        $orders = $this->user->orders()->paginate();

        return fractal()
            ->collection($orders)
            ->withResourceName('orders')
            ->transformWith(OrderTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($orders))
            ->respond();
    }
}
