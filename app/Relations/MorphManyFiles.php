<?php

namespace App\Relations;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @template TRelatedModel of File
 * @extends MorphMany<TRelatedModel>
 */
class MorphManyFiles extends MorphMany
{
    /**
     * Get the related model of the relation.
     *
     * @return TRelatedModel
     */
    public function getRelated(): Model
    {
        return parent::getRelated();
    }
}
