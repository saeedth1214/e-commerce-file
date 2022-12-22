<?php
/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:27 PM
 */

namespace App\Traits;

use Spatie\QueryBuilder\QueryBuilder;

trait FilterQueryBuilder
{

    /**
     * @param $model
     * @return \Spatie\QueryBuilder\QueryBuilder
     */
    public function queryBuilder($model)
    {
        return QueryBuilder::for($model);
    }
}