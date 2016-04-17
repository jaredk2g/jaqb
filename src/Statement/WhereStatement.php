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
        } else {
            // handles #4
            $condition = [$field];

            if (func_num_args() >= 2) {
                // handles #3
                if (is_array($value)) {
                    $operator = 'IN';
                }

                // handles #1 and #2
                $condition[] = $operator;
                $condition[] = $value;
            }

            $this->conditions[] = $condition;
        }

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

    /**
     * Builds a parameterized and escaped SQL fragment
     * for a condition that uses our own internal
     * representation.
     *
     * A condition is represented by an array, and can be
     * have one of the following forms:
     * i) ['SQL fragment']
     * ii) ['identifier', '=', 'value']
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

        // escape the identifier
        $cond[0] = $this->escapeIdentifier($cond[0]);
        if (empty($cond[0])) {
            return '';
        }

        // handle NULL values
        if ($cond[1] === '=' && $cond[2] === null) {
            return $cond[0].' IS NULL';
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

        return implode('', $cond);
    }

    public function build()
    {
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

        return $sql.implode(' AND ', $clauses);
    }
}
