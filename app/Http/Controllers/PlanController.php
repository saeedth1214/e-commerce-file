<?php

namespace App\Http\Controllers;

use App\Traits\FilterQueryBuilder;
use App\Models\Plan;
use App\Transformers\PlanTransformer;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\FilterBySpecialValue;
use App\Filters\FilterByDateTime;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use App\Http\Requests\ChangePlanMediaRequest;
use App\Filters\FilterUniqueValue;
use App\Http\Requests\StorePlanCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Transformers\CommentTransformer;

class PlanController extends Controller
{
    use FilterQueryBuilder;
    private $user = null;

    public function __construct()
    {
        $this->user = auth()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        /**
         * @get('/api/panel/plans')
         * @name('panel.plans.index')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/plans')
         * @name('frontend..plans')
         * @middlewares('api')
         */
        $per_page = request()->input('per_page', 15);

        $plans = $this->queryBuilder(Plan::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('percentage'),
                AllowedFilter::custom('amount', new FilterBySpecialValue),
                AllowedFilter::custom('rebate', new FilterBySpecialValue),
                AllowedFilter::custom('daily_download_limit_count', new FilterBySpecialValue),
                AllowedFilter::custom('created_at', new FilterByDateTime),
                AllowedFilter::custom('unique', new FilterUniqueValue)

            ])->allowedIncludes([
                AllowedInclude::count('users')
            ])
            ->paginate($per_page);

        return fractal()
            ->collection($plans)
            ->withResourceName('plans')
            ->paginateWith(new IlluminatePaginatorAdapter($plans))
            ->transformWith(PlanTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        /**
         * @post('/api/panel/plans')
         * @name('panel.plans.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $planData = $request->safe()->all();
        $plan = Plan::query()->create($planData);
        return fractal()
            ->item($plan)
            ->withResourceName('plans')
            ->transformWith(new PlanTransformer())
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Plan $plan): JsonResponse
    {
        /**
         * @get('/api/panel/plans/{plan}')
         * @name('panel.plans.show')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/plans/{plan}')
         * @name('frontend.show.plans')
         * @middlewares('api')
         */
        return fractal()
            ->item($plan)
            ->withResourceName('plans')
            ->transformWith(PlanTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdatePlanRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/plans/{plan}')
         * @name('panel.plans.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $planData = $request->safe()->all();
        $plan->update($planData);
        return apiResponse()->empty();
    }
    public function uploadFileMedia(Plan $plan, ChangePlanMediaRequest $request)
    {
        /**
         * @post('/api/panel/plans/{plan}/upload-media')
         * @name('panel..plans.media')
         * @middlewares('api', 'auth:sanctum')
         */
        try {
            $plan->addMediaFromRequest('file')
                ->toMediaCollection('plan-image');
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Plan $plan): JsonResponse
    {
        /**
         * @delete('/api/panel/plans/{plan}')
         * @name('panel.plans.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $plan->delete();

        return apiResponse()->empty();
    }

    public function assignComment(StorePlanCommentRequest $request, Plan $plan)
    {
        /**
         * @post('/api/panel/plans/{plan}/comments')
         * @name('panel.plan.comment')
         * @middlewares('api', 'auth:sanctum')
         */
        $commentData = $request->safe()->all();
        $commentData['user_id'] = $this->user->id;
        $comment = $plan->comments()->create($commentData);

        return fractal()
            ->item($comment)
            ->transformWith(CommentTransformer::class)
            ->withResourceName('comments')
            ->respond();
    }

    public function CommentsOfPlan(Plan $plan)
    {
        /**
         * @get('/api/frontend/plans/{plan}/comments')
         * @name('frontend.plan.comments')
         * @middlewares('api')
         */
        $per_page = request('per_page', 15);
        $comments = $plan->acceptedMainComments()->paginate($per_page);
        return fractal()
            ->collection($comments)
            ->transformWith(CommentTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($comments))
            ->withResourceName('comments')
            ->respond();
    }


    public function updateComment(UpdateCommentRequest $request, Plan $plan, int $comment)
    {
        /**
         * @put('/api/panel/plans/{plan}/comments/{comment}')
         * @name('panel.plan.update.comment')
         * @middlewares('api', 'auth:sanctum')
         */

        $updateCommentData = $request->safe()->all();
        $plan->comments()->where('id', $comment)->update($updateCommentData);
        return apiResponse()->empty();
    }
}
