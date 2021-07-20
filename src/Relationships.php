<?php

namespace Tower;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Relationships
{
    public array $columns = ['*'];
    public array $orderBy = [];
    public array $withPivot = [];
    public string $identity = 'id';
    public ?int $limit = null;

    public function select(array $columns): self
    {
        if (in_array('*' , $this->columns))
            unset($this->columns[0]);

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
        $this->orderBy[0] = $column;
        $this->orderBy[1] = $direction;

        return $this;
    }

    public function hasOne(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = null): void
    {
        if (! in_array('*' , $this->columns) && ! in_array($foreignKey , $this->columns))
            $this->select([$foreignKey]);

        $relationships = $this->setHasRelationships($data , $table , $foreignKey , $localKey);

        $localKey = $relationships[1];
        $relationships = $relationships[0];

        $table = Str::singular($table);
        $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
            $item->$table = $relationships->where($foreignKey , $item->$localKey)->first();
        });
    }

    public function  hasMany(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = null): void
    {
        if (! in_array('*' , $this->columns) && ! in_array($foreignKey , $this->columns))
            $this->select([$foreignKey]);

        $relationships = $this->setHasRelationships($data , $table , $foreignKey , $localKey);

        $localKey = $relationships[1];
        $relationships = $relationships[0];

        $table = Str::plural($table);

        if (is_null($this->limit))
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where($foreignKey , $item->$localKey)->values();
            });
        else
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where($foreignKey , $item->$localKey)->take($this->limit)->values();
            });
    }

    public function throughOne(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey): void
    {
        $relationships = $this->setThrough($data , $interfaceTable , $table , $foreignKey , $localKey);

        $table = Str::singular($table);
        $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
            $item->$table = $relationships->where("pivot_$localKey" , $item->id)->first();
        });
    }

    public function throughManyToMany(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey): void
    {
        $relationships = $this->setThrough($data , $interfaceTable , $table , $foreignKey , $localKey);

        $table = Str::plural($table);

        if (is_null($this->limit))
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where("pivot_$localKey" , $item->id)->values();
            });
        else
            $data->map(function ($item) use ($foreignKey , $localKey , $table , $relationships) {
                $item->$table = $relationships->where("pivot_$localKey" , $item->id)->take($this->limit)->values();
            });
    }

    public function polymorphicHasOne(Collection|Paginator $data , string $table , string $type , string $typeName = null , string $idName = null , string $localKey = null): void
    {
        $polymorphic = $this->setPolymorphic($data , $table , $type , $typeName , $idName , $localKey);

        $idName = $polymorphic[1];
        $localKey = $polymorphic[2];
        $relationships = $polymorphic[0];

        $table = Str::singular($table);
        $data->map(function ($item) use ($idName , $localKey , $relationships , $table) {
            $item->$table = $relationships->where($idName , $item->$localKey)->first();
        });
    }

    public function polymorphicManyToMany(Collection|Paginator $data , string $table , string $type , string $typeName = null , string $idName = null , string $localKey = null): void
    {
        $polymorphic = $this->setPolymorphic($data , $table , $type , $typeName , $idName , $localKey);

        $idName = $polymorphic[1];
        $localKey = $polymorphic[2];
        $relationships = $polymorphic[0];

        $table = Str::plural($table);

        if (is_null($this->limit))
            $data->map(function ($item) use ($idName , $localKey , $relationships , $table) {
                $item->$table = $relationships->where($idName , $item->$localKey)->values();
            });
        else
            $data->map(function ($item) use ($idName , $localKey , $relationships , $table) {
                $item->$table = $relationships->where($idName , $item->$localKey)->take($this->limit)->values();
            });
    }

    public function setPolymorphic(Collection|Paginator $data , string $table , string $type , string $typeName = null , string $idName = null , string $localKey = null): array
    {
        $filedName = Str::singular($table) . 'able_';
        $typeName = $typeName ? $filedName . $typeName : $filedName . 'type';
        $idName = $idName ? $filedName . $idName : $filedName . 'id';
        $localKey = $localKey ?: 'id';

        if (! in_array('*' , $this->columns) && ! in_array($idName , $this->columns))
            $this->select([$idName]);

        if (empty($this->orderBy))
            $relationships = DB::table($table)
                ->whereIn($idName , $data->pluck($localKey)->toArray())
                ->where($typeName , $type)
                ->select($this->columns)->get();
        else
            $relationships = DB::table($table)
                ->whereIn($idName , $data->pluck($localKey)->toArray())
                ->where($typeName , $type)
                ->select($this->columns)->orderBy($this->orderBy[0] , $this->orderBy[1])->get();

        return [$relationships , $idName , $localKey];
    }

    public function setThrough(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey): Collection
    {
        $this->withPivot([$localKey]);

        array_walk($this->withPivot , function (&$value) use ($interfaceTable) {
            $value = "$interfaceTable.$value as pivot_$value";
        });

        array_walk($this->columns , function (&$value) use ($table) {
            $value = "$table.$value";
        });

        $columns = array_merge($this->columns , $this->withPivot);

        if (empty($this->orderBy))
            $relationships = DB::table($table)
                ->join($interfaceTable , "$table.$this->identity", '=' , "$interfaceTable.$foreignKey")
                ->whereIn("$interfaceTable.$localKey" , $data->pluck($this->identity)->toArray())
                ->select($columns)
                ->get();
        else
            $relationships = DB::table($table)
                ->join($interfaceTable , "$table.$this->identity", '=' , "$interfaceTable.$foreignKey")
                ->whereIn("$interfaceTable.$localKey" , $data->pluck($this->identity)->toArray())
                ->orderBy($this->orderBy[0] , $this->orderBy[1])
                ->select($columns)
                ->get();

        return $relationships;
    }

    public function setHasRelationships(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = null): array
    {
        $localKey = $localKey ?: 'id';

        if (empty($this->orderBy))
            $relationships = DB::table($table)
                ->whereIn($foreignKey , $data->pluck($localKey)->toArray())
                ->select($this->columns)
                ->get();
        else
            $relationships = DB::table($table)
                ->whereIn($foreignKey , $data->pluck($localKey)->toArray())
                ->select($this->columns)
                ->orderBy($this->orderBy[0] , $this->orderBy[1])
                ->get();

        return [$relationships , $localKey];
    }
}