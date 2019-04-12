<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

abstract class QueryBuilder implements IQueryBuilder
{
    /**
     * @var Builder
     */
    protected $query;

    public function setQuery(Builder $query): IQueryBuilder
    {
        $this->query = $query;

        return $this;
    }
}