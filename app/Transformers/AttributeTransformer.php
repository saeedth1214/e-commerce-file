<?php

namespace App\Transformers;

use App\Models\Attribute;
use League\Fractal\TransformerAbstract;

class AttributeTransformer extends TransformerAbstract
{


    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Attribute $attribute)
    {
        return [
            'id' => $attribute->id,
            'slug' => $attribute->slug,
            'name' => $attribute->name,
            'type_id'=>$attribute->type,
        ];
    }
}
