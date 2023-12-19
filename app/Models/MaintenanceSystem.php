<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use App\Relations\MorphManyFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/** @property-read Collection<int, self> $ancestorsAndSelf */
class MaintenanceSystem extends Model
{
    use HasFiles;
    use HasRecursiveRelationships;

    /** @var array<string> */
    public array $fileRelationships = ['files'];

    /** @return MorphManyFiles<File> */
    public function files(): MorphManyFiles
    {
        return $this->morphManyFiles(File::class, 'fileable');
    }
}
