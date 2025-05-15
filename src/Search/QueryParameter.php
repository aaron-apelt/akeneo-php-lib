<?php

declare(strict_types=1);

namespace AkeneoLib\Search;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;

class QueryParameter extends SearchBuilder
{
    public function resetFilters(): self
    {
        $this->filters = [];

        return $this;
    }

    public function toArray(): array
    {
        $query = [];

        if (! empty($this->filters)) {
            $query['search'] = $this->getFilters();
        }

        return $query;
    }
}
