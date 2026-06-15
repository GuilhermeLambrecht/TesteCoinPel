<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait Searchable
{
    /**
     * Colunas consideradas na busca textual.
     *
     * @return array<int, string>
     */
    abstract protected function searchableColumns(): array;

    /**
     * Busca case-insensitive (funciona em sqlite e postgres) nas colunas pesquisáveis.
     *
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.mb_strtolower($term).'%';
        $columns = $this->searchableColumns();

        return $query->where(function (Builder $builder) use ($columns, $like): void {
            foreach ($columns as $column) {
                $builder->orWhereRaw('lower('.$column.') like ?', [$like]);
            }
        });
    }
}
