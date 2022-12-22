<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Traits\FilterQueryBuilder;
use App\Transformers\CategoryTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\FilterByDateTime;
use App\Http\Requests\changeCategoryMediaRequest;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class CategoryController extends Controller
{
    use FilterQueryBuilder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @get('/api/panel/categories')
         * @name('panel.categories.index')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/categories')
         * @name('frontend.categories')
         * @middlewares('api')
         */
        $per_page = request()->input('per_page', 15);
        $categories = $this->queryBuilder(Category::class)
            ->allowedIncludes([AllowedInclude::count('files')])
            ->allowedFilters([
                'name',
                'slug',
                AllowedFilter::custom('created_at', new FilterByDateTime),
            ])
            ->allowedSorts(['files_count'])
            ->paginate($per_page);


        return fractal()
            ->collection($categories)
            ->withResourceName('categories')
            ->paginateWith(new IlluminatePaginatorAdapter($categories))
            ->transformWith(CategoryTransformer::class)
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        /**
         * @post('/api/panel/categories')
         * @name('panel.categories.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $categoryData = $request->safe()->all();
        $category = Category::query()->create($categoryData);
        return fractal()
            ->item($category)
            ->withResourceName('categories')
            ->transformWith(CategoryTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        /**
         * @get('/api/panel/categories/{category}')
         * @name('panel.categories.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return fractal()
            ->item($category)
            ->withResourceName('categories')
            ->transformWith(CategoryTransformer::class)
            ->respond();
    }


    public function uploadFileMedia(changeCategoryMediaRequest $request, Category $category)
    {
        /**
         * @post('/api/panel/categories/{category}/upload-media')
         * @name('panel..category.media')
         * @middlewares('api', 'auth:sanctum')
         */
        try {
            $category->addMediaFromRequest('file')
                ->toMediaCollection('category-image');
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/categories/{category}')
         * @name('panel.categories.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $categoryData = $request->safe()->all();
        $category->update($categoryData);
        return apiResponse()->empty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        /**
         * @delete('/api/panel/categories/{category}')
         * @name('panel.categories.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $category->delete();
        return apiResponse()->empty();
    }
}
