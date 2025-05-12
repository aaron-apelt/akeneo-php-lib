<?php

declare(strict_types=1);

namespace AkeneoLib\Search;

use Akeneo\Pim\ApiClient\Search\SearchBuilder as AkeneoSearchBuilder;

class SearchBuilder extends AkeneoSearchBuilder
{
    public function resetFilters(): self
    {
        $this->filters = [];
        return $this;
    }
}