<?php

namespace App\Domain;

use App\Entity\Category;

class Search
{
    /**
     * @var ?string
     */
    public ?string $string = '';

    /**
     * @var ?Category[]
     */
    public ?array $categories = [];
}
