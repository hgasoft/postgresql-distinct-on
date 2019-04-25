<?php

namespace DataJoe\Extensions\Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\PostgresGrammar as Base;

class PostgresGrammar extends Base
{
    /**
     * Compile the "select *" portion of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $columns
     *
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if ( ! is_null($query->aggregate)) {
            return;
        }

        $select = 'select ';
        if ($query->distinctOn !== false) {
            $select = "select distinct on({$query->distinctOn}) {$query->distinctOn}, ";
        } elseif ($query->distinct) {
            $select = 'select distinct ';
        }

        return $select . $this->columnize($columns);
    }
}
