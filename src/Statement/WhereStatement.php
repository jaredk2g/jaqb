<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
namespace JAQB\Statement;

use JAQB\Query\SelectQuery;

class WhereStatement extends Statement
{
    /**
     * @var bool
     */
    protected $having;

    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @param bool $having when true, statement becomes a having statement
     */
    public function __construct($having = false)
    {
        $this->having = $having;
    }

    /**
     * Tells whether this statement is a HAVING statement.
     *
     * @return bool true: is HAVING, false: is WHERE
     */
    public function isHaving()
    {
        return $this->having;
    }

    /**
     * Adds a condition to the statement.
     *
     * Accepts the following forms:
     * 1. Equality comparison:
     *      addCondition('username', 'john')
     * 2. Comparison with custom operator:
     *      addCondition('balance', 100, '>')
     * 3. IN statement:
     *      addCondition('group', ['admin', 'owner'])
     * 4. SQL fragment:
     *      addCondition('name LIKE "%john%"')
     * 5. List of conditions to add:
     *      addCondition([['balance', 100, '>'],
     *                    ['user_id', 5]])
     * 6. Map of equality comparisons:
     *      addCondition(['username' => 'john',
     *                    'user_id' => 5])
     * 7. List of SQL fragments:
     *      addCondition(['first_name LIKE "%john%"',
     *                    'last_name LIKE "%doe%"'])
     *
     * @param array|string $field
     * @param string|bool  $value    condition value (optional)
     * @param string       $operator operator (optional)
     *
     * @return self
     */
    public function addCondition($field, $value = false, $operator = '=')
    {
        if (is_array($field) && !$value) {
            foreach ($field as $key => $value) {
                // handles #5
                if (is_array($value)) {
                    call_user_func_array([$this, 'addCondition'], $value);
                // handles #6
                } elseif (!is_numeric($key)) {
                    $this->addCondition($key, $value);
                // handles #7
                } else {
                    $this->addCondition($value);
                }
            }

            return $this;
        }

        // handles #4
        $condition = [$field];

        if (func_num_args() >= 2) {
            // handles #3
            if (is_array($value) && $operator === '=') {
                $operator = 'IN';
            } elseif (is_array($value) && $operator === '<>') {
                $operator = 'NOT IN';
            }

            // handles #1 and #2
            $condition[] = $operator;
            $condition[] = $value;
        }

        $this->conditions[] = $condition;

        return $this;
    }

    public function addConditionOr($field, $value = false, $operator = '=')
    {
        $this->conditions[] = ['OR'];

        return call_user_func_array([$this, 'addCondition'], func_get_args());
    }

    /**
     * Adds a between condition to the query.
     *
     * @param string $field
     * @param mixed  $a     first between value
     * @param mixed  $b     second between value
     *
     * @return self
     */
    public function addBetweenCondition($field, $a, $b)
    {
        $this->conditions[] = [$field, 'BETWEEN', $a, $b];

        return $this;
    }

    /**
     * Adds a not between condition to the query.
     *
     * @param string $field
     * @param mixed  $a     first between value
     * @param mixed  $b     second between value
     *
     * @return self
     */
    public function addNotBetweenCondition($field, $a, $b)
    {
        $this->conditions[] = [$field, 'NOT BETWEEN', $a, $b];

        return $this;
    }

    /**
     * Adds an exists condition to the query.
     *
     * @param callable $f
     *
     * @return self
     */
    public function addExistsCondition(callable $f)
    {
        $this->conditions[] = ['EXISTS', $f];

        return $this;
    }

    /**
     * Adds a not exists condition to the query.
     *
     * @param callable $f
     *
     * @return self
     */
    public function addNotExistsCondition(callable $f)
    {
        $this->conditions[] = ['NOT EXISTS', $f];

        return $this;
    }

    /**
     * Gets the conditions for this statement.
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    public function build()
    {
        // reset the parameterized values
        $this->values = [];

        // build clause from conditions
        $clauses = [];
        foreach ($this->conditions as $condition) {
            $clauses[] = $this->buildClause($condition);
        }

        // remove empty values
        $clauses = array_filter($clauses);

        if (count($clauses) == 0) {
            return '';
        }

        $sql = (!$this->having) ? 'WHERE ' : 'HAVING ';

        return $sql.$this->implodeClauses($clauses);
    }

    /**
     * Builds a parameterized and escaped SQL fragment
     * for a condition that uses our own internal
     * representation.
     *
     * A condition is represented by an array, and can be
     * have one of the following forms:
     * i)   ['SQL fragment']
     * ii)  ['identifier', '=', 'value']
     * iii) ['identifier', 'BETWEEN', 'value', 'value']
     * iv)  ['EXISTS', function(SelectQuery $query) {}]
     *
     * @param array $cond
     *
     * @return string generated SQL fragment
     */
    protected function buildClause(array $cond)
    {
        // handle SQL fragments
        if (count($cond) == 1) {
            return $cond[0];
        }

        // handle EXISTS conditions
        if (in_array($cond[0], ['EXISTS', 'NOT EXISTS'])) {
            $f = $cond[1];
            $query = new SelectQuery();
            $f($query);
            $sql = $query->build();
            $this->values = array_merge($this->values, $query->getValues());

            return $cond[0].' ('.$sql.')';
        }

        // escape the identifier
        $cond[0] = $this->escapeIdentifier($cond[0]);
        if (empty($cond[0])) {
            return '';
        }

        // handle BETWEEN conditions
        if ($cond[1] === 'BETWEEN') {
            return $cond[0].' BETWEEN '.$this->parameterize($cond[2]).' AND '.$this->parameterize($cond[3]);
        } elseif ($cond[1] === 'NOT BETWEEN') {
            return $cond[0].' NOT BETWEEN '.$this->parameterize($cond[2]).' AND '.$this->parameterize($cond[3]);
        }

        // handle NULL values
        if ($cond[1] === '=' && $cond[2] === null) {
            return $cond[0].' IS NULL';
        } elseif ($cond[1] === '<>' && $cond[2] === null) {
            return $cond[0].' IS NOT NULL';
        }

        // handle array values, i.e. for IN conditions
        if (is_array($cond[2])) {
            foreach ($cond[2] as &$value) {
                $value = $this->parameterize($value);
            }
            $cond[2] = '('.implode(',', $cond[2]).')';

        // otherwise parameterize the value
        } else {
            $cond[2] = $this->parameterize($cond[2]);
        }

        return implode(' ', $cond);
    }

    protected function implodeClauses(array $clauses)
    {
        $str = false;
        $or = false;
        foreach ($clauses as $clause) {
            // an 'OR' token will change the operator used
            // when concatenating the next clause
            if ($clause == 'OR') {
                $or = true;
                continue;
            }

            if (!$str) { // first clause needs no operator
                $str = $clause;
            } elseif ($or) {
                $str .= " OR $clause";
            } else {
                $str .= " AND $clause";
            }

            $or = false;
        }

        return $str;
    }
}
