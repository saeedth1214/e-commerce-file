<?php

namespace App\Http\Controllers;

use App\Enums\AttributeTypeEnum;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use App\Traits\FilterQueryBuilder;
use App\Transformers\AttributeTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AttributeController extends Controller
{
    use FilterQueryBuilder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = request()->input('per_page', 15);
        $attributes = $this->queryBuilder(Attribute::class)
            ->allowedFilters([
                'name',
                'slug',
            ])
            ->paginate($per_page);

        $types = [];

        foreach (AttributeTypeEnum::asArray() as $key => $value) {
            $types[] = ['key' => $key, 'value' => $value];
        }

        return fractal()
            ->collection($attributes)
            ->withResourceName('attributes')
            ->paginateWith(new IlluminatePaginatorAdapter($attributes))
            ->transformWith(AttributeTransformer::class)
            ->addMeta(['types'=>$types])
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreAttributeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttributeRequest $request)
    {
        $attributeData = $request->safe()->all();
        $attribute = Attribute::query()->create($attributeData);
        return fractal()
            ->item($attribute)
            ->withResourceName('attributes')
            ->transformWith(AttributeTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $attribute)
    {
        return fractal()
            ->item($attribute)
            ->withResourceName('attributes')
            ->transformWith(AttributeTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateAttributeRequest  $request
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $attributeData = $request->safe()->all();
        $attribute->update($attributeData);
        return apiResponse()->empty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return apiResponse()->empty();
    }
}
