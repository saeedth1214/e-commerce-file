<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\FilterQueryBuilder;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Database\Eloquent\Builder;
use App\Models\File;
use App\Models\Plan;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\UserIndexRequest;
use App\Filters\FilterUniqueValue;
use App\Enums\AccessTypeEnum;
use App\Http\Requests\AssignVoucherToUserRequest;
use App\Traits\AmountAfterModelRebate;

class UserController extends Controller
{
    use FilterQueryBuilder;
    use AmountAfterModelRebate;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserIndexRequest $request): JsonResponse
    {
        /**
         * @get('/api/panel/users')
         * @name('panel.users.index')
         * @middlewares('api', 'auth:sanctum')
         */

        $per_page = $request->input('per_page', 15);
        $users = $this->queryBuilder(User::class)->allowedFilters([
            'first_name',
            'last_name',
            'email',
            'mobile',
            AllowedFilter::custom('unique', new FilterUniqueValue),
        ])
            ->paginate($per_page);

        return fractal()
            ->collection($users)
            ->withResourceName('users')
            ->paginateWith(new IlluminatePaginatorAdapter($users))
            ->transformWith(UserTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        /**
         * @post('/api/panel/users')
         * @name('panel.users.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $userData = $request->safe()->all();
        $user = User::query()->create($userData);

        return fractal()
            ->item($user)
            ->withResourceName('users')
            ->transformWith(UserTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        /**
         * @get('/api/panel/users/{user}')
         * @name('panel.users.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return fractal()
            ->item($user)
            ->withResourceName('users')
            ->transformWith(UserTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateUserRequest $request
     * @param  User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/users/{user}')
         * @name('panel.users.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $userData = $request->safe()->except(['files', 'plans', 'vouchers']);
        $user->update($userData);
        //assign files
        if ($request->has('files')) {
            $files = $request->input('files');
            $callback = fn ($pivot, $accessType = AccessTypeEnum::AdminAdded)
            =>
            [
                $pivot->id => [
                    'amount' => $this->calculateRebate($pivot), 'bought_at' => now(), 'access' => $accessType
                ]
            ];
            $attachments = $this->attachingPivots(
                File::query(),
                $files,
                $callback
            );
            $user->files()->sync($attachments);
        }
        //assign plans
        if ($request->filled('plan_id')) {
            $plan = $request->input('plan_id');

            $callback = fn ($pivot)
            =>
            [
                $pivot->id => [
                    'access' => 0,
                    'activation_at' => now(),
                    'amount' => $this->calculateRebate($pivot),
                    'expired_at' => now()->addDays($pivot->activation_days),
                    'bought_at' => now(),
                ]
            ];
            $attachments = $this->attachingPivots(
                Plan::query(),
                [$plan],
                $callback
            );
            $user->plans()->syncWithoutDetaching($attachments);
        } else if (!$request->filled('Plan_id')) {
            $user->activePlan()->sync($request->input('plan_id'));
        }
        return apiResponse()->empty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        /**
         * @delete('/api/panel/users/{user}')
         * @name('panel.users.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $user->delete();
        return apiResponse()->empty();
    }

    public function changePassword(UpdateUserPasswordRequest $request, User $user)
    {
        /**
         * @patch('/api/panel/users/{user}/change-password')
         * @name('panel..users.password')
         * @middlewares('api', 'auth:sanctum')
         */
        $password = $request->input('password');
        $user->update(compact('password'));
        return apiResponse()->empty();
    }

    public function changeAvatar(UpdateUserAvatarRequest $request, User $user)
    {
        /**
         * @post('/api/panel/users/{user}/change-avatar')
         * @name('panel..users.avatar')
         * @middlewares('api', 'auth:sanctum')
         */
        try {
            $user->addMediaFromRequest('file')
                ->toMediaCollection('avatar-image');
        } catch (FileDoesNotExist $exception) {
            return apiResponse()
                ->status(400)
                ->message('File is missing.')
                ->fail();
        } catch (FileIsTooBig $exception) {
            return apiResponse()
                ->status(400)
                ->message('File is too big.')
                ->fail();
        }
        return apiResponse()->empty();
    }

    private function attachingPivots(Builder $builder, $pivots, $callback)
    {
        if (!count($pivots)) {
            return $pivots;
        }

        $attachments = $builder->whereIn('id', $pivots)
            ->get()
            ->mapToGroups($callback)
            ->map(fn ($group) => $group->first());

        return $attachments->toArray();
    }
    // private function calculate($model)
    // {
    //     if (!$model->rebate) {
    //         return $model->amount;
    //     }
    //     if ($model->rebate && $model->percentage) {
    //         return $model->amount - ($model->amount * ($model->rebate / 100));
    //     }
    //     return $model->amount - $model->rebate;
    // }

    public function assignVouchers(AssignVoucherToUserRequest $request, User $user)
    {
        /**
         * @post('/api/panel/users/{user}/assign-vouchers')
         * @name('panel.user.assign-vouchers')
         * @middlewares('api', 'auth:sanctum')
         */

        $vouchers = $request->vouchers;

        $callback = fn ($pivot) => [
            $pivot['id'] => ['number_authorize_use' => $pivot['authorize_use'], 'number_times_use' => $pivot['times_use']]
        ];

        $attachments = collect($vouchers)->mapToGroups($callback)->map(fn ($group) => $group->first())->toArray();

        $user->vouchers()->sync($attachments);

        return apiResponse()->empty();
    }
}
