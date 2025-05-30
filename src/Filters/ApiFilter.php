<?php

declare(strict_types=1);

namespace App\Filters;

use App\Dto\V1\GetQueryDto;
use Doctrine\ORM\QueryBuilder;

class ApiFilter
{
    protected GetQueryDto $dto;

    protected QueryBuilder $queryBuilder;
    protected array $filterable = [];

    protected array $operatorMap = [
        'eq' => '=',
        'neq' => '<>',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];

    public function set(GetQueryDto $dto, QueryBuilder $queryBuilder): static
    {
        $this->dto = $dto;
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    public function filter(): static
    {
        foreach ($this->dto as $field => $query) {
            if (
                !array_key_exists($field, $this->filterable) ||
                is_null($query)
            ) {
                continue;
            }

            $operator = array_key_first($query);

            if (!array_key_exists($operator, $this->operatorMap)) {
                continue;
            }

            $value = $query[$operator];
            $operator = $this->operatorMap[$operator];
            $rootAlias = $this->queryBuilder->getRootAliases()[0];

            $this->queryBuilder->andWhere("$rootAlias.$field $operator :$field")
                ->setParameter($field, $value);

        }

        return $this;
    }

    public function result(): array
    {
        return $this->queryBuilder
            ->getQuery()
            ->getResult();
    }
}
