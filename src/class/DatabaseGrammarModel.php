<?php

class DatabaseGrammarModel implements DatabaseGrammarInterface {

    protected $tableExtended = [];

    protected $tableStandard = [
        'and' => ' AND ', //string to join 'and' boolean
        'encapLeft' => '(', //string to be placed at the left of encapsulation
        'encapRight' => ')', //string to be placed at the right of encapsulation
        'op_eq' => [
            'stmt' => '? = ?', //statement for EQ operator
            'args' => ['key', 'value'] //argument keys and order for EQ
        ],
        'op_gt' => [
            'stmt' => '? > ?', //statement for GT operator
            'args' => ['key', 'value'] //argument keys and order for GT
        ],
        'op_gte' => [
            'stmt' => '? >= ?', //statement for GTE operator
            'args' => ['key', 'value'] //argument keys and order for GTE
        ],
        'op_in' => [
            'stmt' => '? IN (?)', //statement for IN operator
            'args' => ['key', 'setstring'] //argument keys and order for IN
        ],
        'op_isnull' => [
            'stmt' => '? IS NULL', //statement for ISNULL operator
            'args' => ['key'] //argument keys and order for ISNULL
        ],
        'op_like' => [
            'stmt' => '? LIKE ?', //statement for LIKE operator
            'args' => ['key', 'value'] //argument keys and order for LIKE
        ],
        'op_lt' => [
            'stmt' => '? < ?', //statement for LT operator
            'args' => ['key', 'value'] //argument keys and order for LT
        ],
        'op_lte' => [
            'stmt' => '? <= ?', //statement for LTE operator
            'args' => ['key', 'value'] //argument keys and order for LTE
        ],
        'op_nin' => [
            'stmt' => '? NOT IN (?)', //statment for NIN operator
            'args' => ['key', 'setstring'] //argument keys and order for NIN
        ],
        'op_nisnull' => [
            'stmt' => '? IS NOT NULL', //statement for NISNULL operator
            'args' => ['key'] //argument keys and order for NISNULL
        ],
        'op_nlike' => [
            'stmt' => '? NOT LIKE ?', //statement for NLIKE operator
            'args' => ['key', 'value'] //argument keys and order for NLIKE
        ],
        'op_not' => [
            'stmt' => '? != ?', //statement for NOT operator
            'args' => ['key', 'value'] //argument keys and order for NOT
        ],
        'op_nrange' => [
            'stmt' => '? NOT BETWEEN ? AND ?', //statement for NRANGE operator
            'args' => ['key', 'lower', 'upper'] //argument keys and order for NRANGE
        ],
        'op_nxrange' => [
            'stmt' => '? NOT BETWEEN ? AND ? OR ? = ? OR ? = ?', //statement for NXRANGE operator
            'args' => ['key', 'lower', 'upper', 'key', 'lower', 'key', 'upper'] //argument keys and order for NXRANGE
        ],
        'op_range' => [
            'stmt' => '? BETWEEN ? AND ?', //statement for RANGE operator
            'args' => ['key', 'lower', 'upper'] //argument keys and order for RANGE
        ],
        'op_xrange' => [
            'stmt' => '? BETWEEN ? AND ? AND ? != ? AND ? != ?', //statement for XRANGE operator
            'args' => ['key', 'lower', 'upper', 'key', 'lower', 'key', 'upper'] //argument keys and order for XRANGE
        ],
        'or' => 'OR', //string to join 'or' boolean
        'quoteIdentLeft' => '"', //string to be placed at the left of an identifier
        'quoteIdentRight' => '"', //string to be placed at the right of an identifier
        'quoteStringLeft' => "'", //string to be placed at the left of a string value
        'quoteStringRight' => "'", //string to be placed at the right of a string value
        'setDelimiter' => ',', //delimiter string to use when imploding sets
        'xor' => 'XOR' //string to join 'xor' boolean
    ];

    public static function getToken ($key) {

        //require input
        if (isset($key)) {
            //only allow string input
            if (is_string($key)) {
                //check extended table, fallback to standard table, or return false
                if (isset($this->tableExtended[$key])) { //if extended definition exists...
                    return $this->tableExtended[$key]; //return extended definition.
                } elseif (isset($this->tableStandard[$key])) { //elseif standard definition exists...
                    return $this->tableStandard[$key]; //return standard definition.
                } else {
                    return false;
                }
            } else {
                throw new DatabaseException(
                    __METHOD__.'(): input of type other than string.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                    $this
                );
                return false;
            }
        } else {
            throw new DatabaseException(
                __METHOD__.'(): missing required argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT,
                $this
            );
            return false;
        }

    }

}

?>
