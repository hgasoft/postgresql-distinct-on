<?php

namespace DataJoe\Extensions\Illuminate\Database\Query;

use Illuminate\Database\Query\Builder as Base;

class Builder extends Base
{
    /**
     * Indicates if the query returns distinct results.
     *
     * @var bool|string
     */
    public $distinctOn = false;

    /**
     * Force the query to only return distinct on results.
     *
     * @param string $fieldName
     *
     * @return $this
     */
    public function distinctOn(string $fieldName)
    {
        $this->distinctOn = $fieldName;

        return $this;
    }
}
