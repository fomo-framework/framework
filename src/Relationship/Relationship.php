<?php

namespace Fomo\Relationship;

use Fomo\Database\DB;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Relationship
{
    protected array $columns = ['*'];
    protected array $orderBy = [];
    protected array $withPivot = [];
    protected string $identity = 'id';
    protected ?int $limit = null;
    protected array $where = [];
    protected array $orWhere = [];
    protected array $whereNull = [];
    protected array $whereNotNull = [];
    protected array $whereIn = [];
    protected array $whereNotIn = [];
    protected array $whereRaw = [];

    public function where(string|callable $column , mixed $operator = '=' , mixed $value = null): self
    {
        $this->where[] = [
            'column' => $column ,
            'operator' => $operator ,
            'value' => $value ,
        ];

        return $this;
    }

    public function orWhere(string|callable $column , mixed $operator = '=' , mixed $value = null): self
    {
        $this->orWhere[] = [
            'column' => $column ,
            'operator' => $operator ,
            'value' => $value ,
        ];

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->whereNull[] = [
            'column' => $column
        ];

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->whereNotNull[] = [
            'column' => $column
        ];

        return $this;
    }

    public function whereIn(string $column , array $values): self
    {
        $this->whereIn[] = [
            'column' => $column ,
            'values' => $values
        ];

        return $this;
    }

    public function whereNotIn(string $column , array $values): self
    {
        $this->whereNotIn[] = [
            'column' => $column ,
            'values' => $values
        ];

        return $this;
    }

    public function whereRaw(string $raw): self
    {
        $this->whereRaw[] = [
            'raw' => $raw
        ];

        return $this;
    }

    public function select(array $columns): self
    {
        if (in_array('*' , $this->columns)){
            unset($this->columns[0]);
        }

        $this->columns = array_merge($this->columns , $columns);

        return $this;
    }

    public function identity(string $identity): self
    {
        $this->identity = $identity;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function withPivot(array $columns): self
    {
        $this->withPivot = array_merge($this->withPivot , $columns);

        return $this;
    }

    public function withTimestamp(): self
    {
        $this->withPivot = array_merge($this->withPivot , ['created_at' , 'updated_at']);

        return $this;
    }

    public function orderBy(string $column , string $direction = 'desc'): self
    {
        $this->orderBy['column'] = $column;
        $this->orderBy['direction'] = $direction;

        return $this;
    }

    public function hasOne(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = 'id'): void
    {
        if (! in_array('*' , $this->columns) && ! in_array($foreignKey , $this->columns)){
            $this->select([$foreignKey]);
        }

        $relationships = $this->setHasRelationships($data , $table , $foreignKey , $localKey);

        $localKey = $relationships[1];
        $relationships = $relationships[0];

        $table = Str::singular($table);
        $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
            $item->$table = $relationships->where($foreignKey , $item->$localKey)->first();
        });
    }

    public function hasMany(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = 'id'): void
    {
        if (! in_array('*' , $this->columns) && ! in_array($foreignKey , $this->columns)){
            $this->select([$foreignKey]);
        }

        $relationships = $this->setHasRelationships($data , $table , $foreignKey , $localKey);

        $localKey = $relationships[1];
        $relationships = $relationships[0];

        $table = Str::plural($table);

        if (is_null($this->limit)){
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where($foreignKey , $item->$localKey)->values();
            });
        } else{
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where($foreignKey , $item->$localKey)->take($this->limit)->values();
            });
        }
    }

    public function throughHasOne(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey): void
    {
        $relationships = $this->setThrough($data , $interfaceTable , $table , $foreignKey , $localKey);

        $table = Str::singular($table);
        $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
            $item->$table = $relationships->where("pivot_$localKey" , $item->id)->first();
        });
    }

    public function throughHasMany(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey): void
    {
        $relationships = $this->setThrough($data , $interfaceTable , $table , $foreignKey , $localKey);

        $table = Str::plural($table);

        if (is_null($this->limit)){
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where("pivot_$localKey" , $item->id)->values();
            });
        } else{
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where("pivot_$localKey" , $item->id)->take($this->limit)->values();
            });
        }
    }

    public function polymorphicHasOne(Collection|Paginator $data , string $table , string $type , string $typeKey , string $identityKey , string $localKey = 'id'): void
    {
        $polymorphic = $this->setPolymorphic($data , $table , $type , $typeKey , $identityKey , $localKey);

        $idName = $polymorphic[1];
        $localKey = $polymorphic[2];
        $relationships = $polymorphic[0];

        $table = Str::singular($table);
        $data->map(function ($item) use ($idName , $localKey , $relationships , $table) {
            $item->$table = $relationships->where($idName , $item->$localKey)->first();
        });
    }

    public function polymorphicHasMany(Collection|Paginator $data , string $table , string $type , string $typeKey , string $identityKey , string $localKey = 'id'): void
    {
        $polymorphic = $this->setPolymorphic($data , $table , $type , $typeKey , $identityKey , $localKey);

        $idName = $polymorphic[1];
        $localKey = $polymorphic[2];
        $relationships = $polymorphic[0];

        $table = Str::plural($table);

        if (is_null($this->limit)){
            $data->map(function ($item) use ($idName , $localKey , $relationships , $table) {
                $item->$table = $relationships->where($idName , $item->$localKey)->values();
            });
        } else{
            $data->map(function ($item) use ($idName , $localKey , $relationships , $table) {
                $item->$table = $relationships->where($idName , $item->$localKey)->take($this->limit)->values();
            });
        }
    }

    protected function setPolymorphic(Collection|Paginator $data , string $table , string $type , string $typeKey , string $identityKey , string $localKey): array
    {
        if (! in_array('*' , $this->columns) && ! in_array($identityKey , $this->columns)){
            $this->select([$identityKey]);
        }

        $relationships = DB::table($table)
            ->whereIn($identityKey , $data->pluck($localKey)->toArray())
            ->where($typeKey , $type);

        return [$this->setConditionsAndGetData($relationships , $this->columns) , $identityKey , $localKey];
    }

    protected function setThrough(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey): Collection
    {
        $this->withPivot([$localKey]);

        array_walk($this->withPivot , function (&$value) use ($interfaceTable) {
            $value = "$interfaceTable.$value as pivot_$value";
        });

        array_walk($this->columns , function (&$value) use ($table) {
            $value = "$table.$value";
        });

        $columns = array_merge($this->columns , $this->withPivot);

        $relationships = DB::table($table)
            ->join($interfaceTable , "$table.$this->identity", '=' , "$interfaceTable.$foreignKey")
            ->whereIn("$interfaceTable.$localKey" , $data->pluck($this->identity)->toArray());

        return $this->setConditionsAndGetData($relationships , $columns);
    }

    protected function setHasRelationships(Collection|Paginator $data , string $table , string $foreignKey, string $localKey): array
    {
        $relationships = DB::table($table)
            ->whereIn($foreignKey , $data->pluck($localKey)->toArray());

        return [$this->setConditionsAndGetData($relationships , $this->columns) , $localKey];
    }

    protected function setConditionsAndGetData(Builder $query , array $columns): Collection
    {
        if (!empty($this->where)){
            foreach ($this->where as $value){
                is_callable($value['column']) ?
                    $query->where($value['column']) :
                    $query->where($value['column'] , $value['operator'] , $value['value']);
            }
        }

        if (!empty($this->orWhere)){
            foreach ($this->orWhere as $value){
                is_callable($value['column']) ?
                    $query->where($value['column']) :
                    $query->where($value['column'] , $value['operator'] , $value['value']);
            }
        }

        if (!empty($this->whereNull)){
            foreach ($this->whereNull as $value){
                $query->whereNull($value['column']);
            }
        }

        if (!empty($this->whereNotNull)){
            foreach ($this->whereNotNull as $value){
                $query->whereNotNull($value['column']);
            }
        }

        if (!empty($this->whereIn)){
            foreach ($this->whereIn as $value){
                $query->whereIn($value['column'] , $value['values']);
            }
        }

        if (!empty($this->whereNotIn)){
            foreach ($this->whereNotIn as $value){
                $query->whereNotIn($value['column'] , $value['values']);
            }
        }

        if (!empty($this->whereRaw)){
            foreach ($this->whereRaw as $value){
                $query->whereRaw($value['raw']);
            }
        }

        if (!empty($this->orderBy)){
            $query->orderBy($this->orderBy['column'] , $this->orderBy['direction']);
        }

        return $query->select($columns)->get();
    }
}