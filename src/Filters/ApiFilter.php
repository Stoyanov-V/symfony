<?php

declare(strict_types=1);

namespace App\Filters;

use App\Dto\V1\GetQueryDto;
use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class ApiFilter
{
    protected GetQueryDto $dto;

    protected QueryBuilder $queryBuilder;

    protected string $rootAlias;

    /** @var array<string, array<string>> */
    protected array $filterable = [];

    /** @var array<string> */
    protected array $sortable = [];

    /** @var array<string, string> */
    protected array $operatorMap = [
        'eq' => '=',
        'neq' => '<>',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];

    /**
     * @var array<string, string>
     */
    protected array $context = [];

    public function set(GetQueryDto $dto, QueryBuilder $queryBuilder): static
    {
        $this->dto = $dto;
        $this->queryBuilder = $queryBuilder;
        $this->rootAlias = $queryBuilder->getRootAliases()[0];

        return $this;
    }

    public function filter(): static
    {
        foreach ($this->filterable as $field => $operators) {

            $query = $this->dto->{$field};

            if (is_null($query)) {
                continue;
            }

            $operator = array_key_first($query);

            if (!array_key_exists($operator, $this->operatorMap)) {
                continue;
            }

            $value = $query[$operator];
            $operator = $this->operatorMap[$operator];

            $this->queryBuilder->andWhere("$this->rootAlias.$field $operator :$field")
                ->setParameter($field, $value);
        }

        return $this;
    }

    public function sort(): static
    {
        if (is_null($this->dto->sort)) {
            return $this;
        }

        $fields = explode(',', $this->dto->sort);

        foreach ($fields as $field) {
            $column = ltrim($field, '-');

            if(!in_array($column, $this->sortable)) {
                continue;
            }

            $direction = str_starts_with($field, '-') ? 'desc' : 'ASC';

            $this->queryBuilder->addOrderBy("$this->rootAlias.$column", $direction);

        }

        return $this;
    }

    /**
     * @return array{
     *     data: ArrayIterator<int, object>,
     *     total: int,
     *     current_page: int,
     *     from: float,
     *     per_page: int
     * }
     * @throws Exception
     */
    public function paginate(): array
    {
        $limit = $this->dto->perPage;
        $offset = ($this->dto->page - 1) * $limit;
        $this->queryBuilder->setMaxResults($limit)
            ->setFirstResult($offset);

        $paginator = new Paginator($this->queryBuilder);

        return [
            'data' => $paginator->getIterator(),
            'total' => $paginator->count(),
            'current_page' => $this->dto->page,
            'from' => ceil($paginator->count() / $limit),
            'per_page' => $this->dto->perPage,
        ];
    }

    /**
     * @return array{
     *     groups: array<string>
     * }
     */
    public function include(GetQueryDto $dto): array
    {
        $context = [
            'groups' => [$this->context['default']]
        ];

        if (is_null($dto->include)) {
            return $context;
        }

        $includes = explode(',', $dto->include);

        foreach ($includes as $include) {
            if (!array_key_exists($include, $this->context)) {
                continue;
            }

            $context['groups'][] = $this->context[$include];
        }

        return $context;
    }

    /**
     * @return array<object>
     */
    public function result(): array
    {
        return $this->queryBuilder
            ->getQuery()
            ->getResult();
    }
}
