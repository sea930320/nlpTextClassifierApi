<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToUser
{
    /**
     * @param bool $excludeDeleted
     *
     * @return Builder
     */
    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where($this->getTable() . '.user_id', '=', auth()->user()->id);
    }
}
