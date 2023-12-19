<?php

namespace App\Models\Concerns;

use App\Relations\MorphManyFiles;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** @mixin Model */
trait HasFiles
{
    protected static function bootHasFiles(): void
    {
        static::registerModelEvent('deleting', function ($model): void {
            if (is_array($model->fileRelationships)) {
                return;
            }

            throw new Exception('Model: '.$model::class.' does not have required property.');
        });

        static::registerModelEvent('forceDeleted', fn ($model) => $model->deleteAllFiles());
        static::registerModelEvent('restored', fn ($model) => $model->restoreAllFiles());
    }

    public function addFiles(string $relation, array $ids = []): void
    {
        // load scoped files by ID
        $files = $this->{$relation}()->getRelated()->whereIn('id', $ids)->get()->keyBy('id');

        // associate files with the model through the selected relationship
        foreach ($files as $file) {
            $this->{$relation}()->save($file);
        }
    }

    public function removeFiles(string $relation, array $ids = []): void
    {
        // load scoped files by ID
        $files = $this->{$relation}()->getRelated()->whereIn('id', $ids)->get()->keyBy('id');

        // dissociate selected files from their fileable() relation
        foreach ($files as $file) {
            $file->fileable()->dissociate();
            $file->save();
        }
    }

    public function deleteAllFiles(): void
    {
        foreach ($this->fileRelationships as $relationship) {
            if (empty($this->{$relationship})) {
                continue;
            }

            if ($this->{$relationship} instanceof Model) {
                $this->{$relationship}->delete();
            } else {
                foreach ($this->{$relationship} as $relation) {
                    $relation->delete();
                }
            }
        }
    }

    // This method causes bladestan to throw an exception
    public function restoreAllFiles(): void
    {
        foreach ($this->fileRelationships as $relationship) {
            $loaded = $this->{$relationship}()->withTrashed()->get();

            foreach ($loaded as $relation) {
                $relation->restore();
            }
        }
    }

    /**
     * Define a polymorphic one-to-many relationship.
     */
    public function morphManyFiles(
        string $related,
        string $name,
        ?string $type = null,
        ?string $id = null,
        ?string $localKey = null,
    ): MorphManyFiles {
        $instance = $this->newRelatedInstance($related);

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        [$type, $id] = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newMorphManyFiles($instance->newQuery(), $this, $table . '.' . $type, $table . '.' . $id, $localKey);
    }

    /**
     * Instantiate a new MorphFiles relationship.
     *
     * @param string $type
     * @param string $id
     * @param string $localKey
     * @return MorphManyFiles
     */
    protected function newMorphManyFiles(Builder $query, Model $parent, $type, $id, $localKey): MorphManyFiles
    {
        return new MorphManyFiles($query, $parent, $type, $id, $localKey);
    }
}
