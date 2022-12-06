<?php

namespace Fomo\Facades;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Fomo\Relationship\Relationship as MainRelationship;

/**
 * @method static MainRelationship where(string|callable $column , mixed $operator = '=' , mixed $value = null)
 * @method static MainRelationship orWhere(string|callable $column , mixed $operator = '=' , mixed $value = null)
 * @method static MainRelationship whereNull(string $column)
 * @method static MainRelationship whereNotNull(string $column)
 * @method static MainRelationship whereIn(string $column , array $values)
 * @method static MainRelationship whereNotIn(string $column , array $values)
 * @method static MainRelationship whereRaw(string $raw)
 * @method static MainRelationship select(array $columns)
 * @method static MainRelationship identity(string $identity)
 * @method static MainRelationship limit(int $limit)
 * @method static MainRelationship withPivot(array $columns)
 * @method static MainRelationship withTimestamp()
 * @method static MainRelationship orderBy(string $column , string $direction = 'desc')
 * @method static void hasOne(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = 'id')
 * @method static void hasMany(Collection|Paginator $data , string $table , string $foreignKey, string $localKey = 'id')
 * @method static void throughHasOne(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey)
 * @method static void throughHasMany(Collection|Paginator $data , string $interfaceTable , string $table, string $foreignKey, string $localKey)
 * @method static void polymorphicHasOne(Collection|Paginator $data , string $table , string $type , string $typeKey , string $identityKey , string $localKey = 'id')
 * @method static void polymorphicHasMany(Collection|Paginator $data , string $table , string $type , string $typeKey , string $identityKey , string $localKey = 'id')
 */
class Relationship extends Facade
{
    protected static function getMainClass(): string
    {
        return 'relationship';
    }
}