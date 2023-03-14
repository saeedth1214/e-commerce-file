<?php

namespace App\Http\Controllers;

use App\Traits\FilterQueryBuilder;
use App\Models\Tag;
use App\Filters\FilterByDateTime;
use App\Http\Requests\StoreTagRequest;
use Illuminate\Http\JsonResponse;
use App\Transformers\TagTransformer;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Support\Facades\Cache;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\QueryBuilder\AllowedFilter;

class TagController extends Controller
{
    use FilterQueryBuilder;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        /**
         * @get('/api/panel/tags')
         * @name('panel.tags.index')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/tags')
         * @name('frontend..tags')
         * @middlewares('api')
         */
        $per_page = request()->input('per_page', 15);

        $tags = $this->queryBuilder(Tag::class)
            ->allowedSorts('created_at')
            ->allowedFilters([
                'name',
                'slug',
                AllowedFilter::custom('created_at', new FilterByDateTime),
            ])
            ->paginate($per_page);


        return fractal()
            ->collection($tags)
            ->withResourceName('tags')
            ->paginateWith(new IlluminatePaginatorAdapter($tags))
            ->transformWith(TagTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreTagRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        /**
         * @post('/api/panel/tags')
         * @name('panel.tags.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $tagData = $request->safe()->all();

        $tag = Tag::query()->create($tagData);

        return fractal()
            ->item($tag)
            ->withResourceName('tags')
            ->transformWith(TagTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tag $tag): JsonResponse
    {
        /**
         * @get('/api/panel/tags/{tag}')
         * @name('panel.tags.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return fractal()
            ->item($tag)
            ->withResourceName('tags')
            ->transformWith(TagTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/tags/{tag}')
         * @name('panel.tags.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $tagData = $request->safe()->all();

        $tag->update($tagData);

        return apiResponse()->empty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tag $tag): JsonResponse
    {
        /**
         * @delete('/api/panel/tags/{tag}')
         * @name('panel.tags.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $tag->delete();

        return apiResponse()->empty();
    }

    public function landingPage()
    {

        $tags = Cache::remember('landingtags', 86400, function () {
            return Tag::query()->latest()->take(4)->get();
        });

        return fractal()
            ->collection($tags)
            ->transformWith(new TagTransformer())
            ->withResourceName('tags')
            ->respond();
    }
}
