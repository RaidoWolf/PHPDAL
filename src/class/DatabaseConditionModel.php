<?php

class DatabaseConditionModel implements DatabaseConditionInterface {

    /*
     * DBMS-specific Grammar Table
     */
    protected $dbmsGrammarTable = [
        //only put data here in extended classes
    ];

    /*
     * Standards-Compliant SQL Grammar Table
     * (don't mess with this one)
     */
    protected $standardGrammarTable = [
        'and'               => ' AND ', //string to join 'and' boolean
        'encapLeft'         => '(', //string to be placed at the left of encapsulation
        'encapRight'        => ')', //string to be placed at the right of encapsulation
        'op_eq'             => [
            'stmt'              => '? = ?', //statement for EQ operator
            'args'              => ['key','value'] //argument keys and order for EQ
        ],
        'op_gt'             => [
            'stmt'              => '? > ?', //statement for GT operator
            'args'              => ['key','value'] //argument keys and order for GT
        ],
        'op_gte'            => [
            'stmt'              => '? >= ?', //statement for GTE operator
            'args'              => ['key','value'] //argument keys and order for GTE
        ],
        'op_in'             => [
            'stmt'              => '? IN (?)', //statement for IN operator
            'args'              => ['key','setstring'] //argument keys and order for IN
        ],
        'op_isnull'         => [
            'stmt'              => '? IS NULL', //statement for ISNULL operator
            'args'              => ['key'] //argument keys and order for ISNULL
        ],
        'op_like'           => [
            'stmt'              => '? LIKE ?', //statement for LIKE operator
            'args'              => ['key', 'value'] //argument keys and order for LIKE
        ],
        'op_lt'             => [
            'stmt'              => '? < ?', //statement for LT operator
            'args'              => ['key','value'] //argument keys and order for LT
        ],
        'op_lte'            => [
            'stmt'              => '? <= ?', //statement for LTE operator
            'args'              => ['key','value'] //argument keys and order for LTE
        ],
        'op_nin'            => [
            'stmt'              => '? NOT IN (?)', //statement for NIN operator
            'args'              => ['key','setstring'] //argument keys and order for NIN
        ],
        'op_nisnull'        => [
            'stmt'              => '? IS NOT NULL', //statement for NISNULL operator
            'args'              => ['key'] //argument keys and order for NISNULL
        ],
        'op_nlike'          => [
            'stmt'              => '? NOT LIKE ?', //statement for NLIKE operator
            'args'              => ['key', 'value'] //argument keys and order for NLIKE
        ],
        'op_not'            => [
            'stmt'              => '? != ?', //statement for NOT operator
            'args'              => ['key','value'] //argument keys and order for NOT
        ],
        'op_nrange'         => [
            'stmt'              => '? NOT BETWEEN ? AND ?', //statement for NRANGE operator
            'args'              => ['key','lower','upper'] //argument keys and order for NRANGE
        ],
        'op_nxrange'        => [
            'stmt'              => '? NOT BETWEEN ? AND ? OR ? = ? OR ? = ?', //statement for NXRANGE operator
            'args'              => ['key','lower','upper','key','lower','key','upper'] //argument keys and order for NXRANGE
        ],
        'op_range'          => [
            'stmt'              => '? BETWEEN ? AND ?', //statement for RANGE operator
            'args'              => ['key','lower','upper'] //argument keys and order for RANGE
        ],
        'op_xrange'         => [
            'stmt'              => '? BETWEEN ? AND ? AND ? != ? AND ? != ?', //statement for XRANGE operator
            'args'              => ['key','lower','upper','key','lower','key','upper'] //argument keys and order for XRANGE
        ],
        'or'                => 'OR', //string to join 'or' boolean
        'quoteStringLeft'   => "'", //string to be placed at the left of a string value
        'quoteStringRight'  => "'", //string to be placed at the right of a string value
        'setDelimiter'      => ',', //delimiter string to use when imploding sets
        'xor'               => 'XOR' //string to join 'xor' boolean
    ];

    protected $statement = [];
    protected $structure = [];

    /**
     * Constructor Method
     * @param unknown $struct
     */
    public function __construct ($struct = []) {

        $this->structure = $struct;
        $this->statement = $this->parse($struct);

    }

    /**
     * Invocation Method
     * @return string - generated statement
     * @see DatabaseConditionInterface::__invoke()
     */
    public function __invoke () {

        return $this->statement;

    }

    /**
     * String Type Conversion Method
     * @return string - generated statement
     * @see DatabaseConditionInterface::__toString()
     */
    public function __toString () {

        return $this->statement;

    }

    public function add ($rule) {
    
        $typeof_rule = gettype($rule);
        $typeof_structure = gettype($this->structure);
        if ($typeof_rule == 'array') {
            if ($typeof_structure == 'array') {
                //merge the arrays and generate statement string
                $this->structure = array_unique(array_merge($rule, $this->structure));
                $this->statement = $this->parse($this->structure);
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'() failed due to structure array of invalid type encountered as structure object.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'() failed due to structure array of invalid type given as rule to add.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }
    
    }

    public function del ($rule) {

        $typeof_rule = gettype($rule);
        if ($typeof_rule == 'array') {
            $this->structure = array_diff($this->structure, $rule);
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'() failed due to structure array of invalid type either as argument or in condition object.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

    }

    /**
     * DatabaseConditionModel->getGrammar() Method
     * 
     * Looks up a value in the grammar tables.
     * 
     * @param string $key
     * @return string|boolean
     * @see DatabaseConditionInterface::getGrammar()
     */
    protected function getGrammar ($key) {

        //require input
        if (isset($key)) {
            //only allow string input
            if (is_string($key)) {
                //check DBMS grammar table, fallback to standard, or return false
                if (isset($this->dbmsGrammarTable[$key])) {
                    return $this->dbmsGrammarTable[$key];
                } elseif (isset($this->standardGrammarTable[$key])) {
                    return $this->standardGrammarTable[$key];
                } else {
                    return false;
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'() failed due to input of type other than string.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'() failed due to missing required argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
        }

    }

    public function getStatement () {

        return $this->statement;

    }

    public function getStructure () {

        return $this->structure;

    }

    /**
     * DatabaseConditionModel->parse() Method
     *
     * Converts an associative array of SQL boolean logic conditions to a string
     * that can be used in a standard SQL query.
     *
     * @param array $array
     * @param bool $encap
     * @return array
     *
     * The following is a basic reference of the options:
     *
     *  defaults:
     *      if conditions are not wrapped in an AND, OR, or XOR array, then it will
     *      be assumed that they are to be treated as an AND block.
     *  input types:
     *      Values for conditions must be SCALAR. It will natively handle strings,
     *      integers, floats, and booleans. No other types are allowed, as no other
     *      types are valid in the SQL language. Note that false and 'false' are
     *      different in PHP. If you didn't know that, you might want to look into
     *      PHP data types a little further to get a firm understanding.
     *  Operations ('type' values):
     *      |---------------|-----------------------------------------------------------------------------------------------|
     *      |     TYPE      |       DESCRIPTION                                                                             |
     *      |---------------|-----------------------------------------------------------------------------------------------|
     *      =, EQ           - EQUAL - Checks if 'key' is equal to 'value'
     *      !, NOT          - NOT - Checks if 'key' is not equal to 'value'
     *      <, LT           - LESS THAN - Checks if 'key' is less than 'value'
     *      <=, LTE         - LESS THAN OR EQUAL - Checks if 'key' is less than or equal to 'value'.
     *      >, GT           - GREATER THAN - Checks if 'key' is greater than 'value'
     *      >=, GTE         - GREATER THAN OR EQUAL - Checks if 'key' is greater than or equal to 'value'.
     *      <>, RANGE       - RANGE - Checks if 'key' is between 'lower' and 'upper'.
     *      <x>, XRANGE     - EXCLUSIVE RANGE - Checks if 'key' is between, but not equal to, 'lower' and 'upper'
     *      !<>, NRANGE     - NOT RANGE - Checks if 'key' is outside of 'lower' and 'upper'
     *      !<x>, NXRANGE   - NOT EXCLUSIVE RANGE - Checks if 'key' is equal to or outside 'lower' and 'upper'
     *      [], IN          - IN - Checks if 'key' is in the set (array) 'set'.
     *      ![], NIN        - NOT IN - Checks if 'key' is not in the set (array) 'set'.
     *      ~, LIKE         - LIKE - Uses database driver's pattern matching to check if 'key' is like 'value'.
     *      !~, NLIKE       - NOT LIKE - Uses database driver's pattern matching to check if 'key' is not like 'value'.
     *      :0, ISNULL      - IS - Tests if 'key' is a null value.
     *      !:0, NISNUL     - IS NOT - Tests if 'key' is not a null value.
     *
     */
    public function parse (array $array, $encap = false) {
    
        $outArray = [
            'stmt' => '',
            'args' => []
        ];
        $tmpStmt = [];
    
        $typeof_array = gettype($array);
        if ($typeof_array == 'array') {
            //array is empty (wildcard)
            if (count($array) == 0) {
                $condType = 'NONE';
                $tmpStmt = [];
            } else {
                //array has conditions (or at least content)
                foreach ($array as $value) {
                    $condType = 'AND';
                    $tmp = [];
                    $typeof_value = gettype($value);
                    if ($typeof_value == 'array') {
                        if (array_key_exists('%', $value)) {
                            $comparator = $value['%'];
                        }
                        if (in_array($comparator, ['AND', 'OR', 'XOR'])) {
                            $condType = $key;
                            $tmp = $this->parse($value, true);
                        } else {
                            $type = $value['type'];
                            $key = (string)$key;
                            if (isset($value['value'])) {
                                if (!is_scalar($value['value'])) {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to input value not scalar.',
                                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                                    );
                                }
                                $cValue = (string)$value['value'];
                            }
    
                            //validate input for operations structures
                            if (isset($value['key'])) {
                                $cKey = (string)$value['key'];
                            } else {
                                throw new DatabaseException(
                                    $this,
                                    __CLASS__.'->'.__METHOD__.'() failed due to missing "key" value in structure.',
                                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                );
                            }
                            if (isset($value['value'])) {
                                $cValue = (string)$value['value'];
                            } else {
                                if (in_array($type, [
                                    '=', 'EQ', '!', 'NOT', ':', 'IS', '!:', 'NIS',
                                    '<', 'LT', '<=', 'LTE', '>', 'GT', '>=', 'GTE',
                                    '~', 'LIKE', '!~', 'NLIKE'
                                ])) {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to missing required "value" value in structure.',
                                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                    );
                                }
                            }
                            if (isset($value['lower'])) {
                                $cLower = (string)$value['lower'];
                            } else {
                                if (in_array($type, [
                                    '<>', 'RANGE', '!<>', 'NRANGE', '<x>', 'XRANGE',
                                    '!<x>', 'NXRANGE'
                                ])) {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to missing required "lower" value in structure.',
                                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                    );
                                }
                            }
                            if (isset($value['upper'])) {
                                $cUpper = (string)$value['upper'];
                            } else {
                                if (in_array($type, [
                                    '<>', 'RANGE', '!<>', 'NRANGE', '<x>', 'XRANGE',
                                    '!<x>', 'NXRANGE'
                                ])) {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to missing required "upper" value in structure.',
                                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                    );
                                }
                            }
                            if (isset($value['set'])) {
                                $typeof_cset = gettype($value['set']);
                                if ($typeof_cset == 'array') {
                                    $cSet = $value['set'];
                                } elseif ($typeof_cset == 'string') {
                                    $cSet = str_getcsv($value['set'], ',', '"', '\\');
                                } else {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to "set" value of invalid type in structure.',
                                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                                    );
                                }
                                $cSetLength = count($cSet);
                            } else {
                                if (in_array($type, [
                                    '[]', 'IN', '![]', 'NIN'
                                ])) {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to missing required "set" value in structure.',
                                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                    );
                                }
                            }

                            //if a set is given, convert it into a single string
                            if (isset($value['set'])) {
                                $value['setstring'] = implode($this->getGrammar('setDelimiter'), $value['set']);
                            }

                            //parse operations structures
                            if          ($type == '='       || $type == 'EQ')       {
                                $grammar = $this->getGrammar('op_eq');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!'       || $type == 'NOT')      {
                                $grammar = $this->getGrammar('op_not');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<'       || $type == 'LT')       {
                                $grammar = $this->getGrammar('op_lt');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<='      || $type == 'LTE')      {
                                $grammar = $this->getGrammar('op_lte');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '>'       || $type == 'GT')       {
                                $grammar = $this->getGrammar('op_gt');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '>='      || $type == 'GTE')      {
                                $grammar = $this->getGrammar('op_gte');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<>'      || $type == 'RANGE')    {
                                $grammar = $this->getGrammar('op_range');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<x>'     || $type == 'XRANGE')   {
                                $grammar = $this->getGrammar('op_xrange');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!<>'     || $type == 'NRANGE')   {
                                $grammar = $this->getGrammar('op_nrange');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => [],
                                ];
                            } elseif    ($type == '!<x>'    || $type == 'NXRANGE')  {
                                $grammar = $this->getGrammar('op_nxrange');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '[]'      || $type == 'IN')       {
                                //TODO: escape each value in the set separately
                                $grammar = $this->getGrammar('op_in');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '![]'     || $type == 'NIN')      {
                                //TODO: escape each value in the set separately
                                $grammar = $this->getGrammar('op_nin');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '~'       || $type == 'LIKE')     {
                                $grammar = $this->getGrammar('op_like');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!~'      || $type == 'NLIKE')    {
                                $grammar = $this->getGrammar('op_nlike');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == ':0'      || $type == 'ISNULL')   {
                                $grammar = $this->getGrammar('op_isnull');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!:0'     || $type == 'NISNULL')  {
                                $grammar = $this->getGrammar('op_nisnull');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } else {
                                //given comparison type invalid
                                throw new DatabaseException(
                                    $this,
                                    __CLASS__.'->'.__METHOD__.'() failed due to an invalid comparison type encountered.',
                                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                );
                            }

                            //insert all arguments into $tmp['args']
                            foreach ($grammar['args'] as $arg) {
                                if (!isset($value[$arg])) {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'() failed due to a missing required argument in the structure.',
                                        DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
                                    );
                                }
                                $tmp['args'][] = $value[$arg];
                            }

                        }
                    } elseif ($typeof_value == 'string') {
                        //wildcard condition
                        if ($value == '*') {
                            $condType = 'OR';
                            $tmpStmt = [];
                            break;
                        }
                    } else {
                        throw new DatabaseException(
                            $this,
                            __CLASS__.'->'.__METHOD__.'() failed due to block of invalid type encountered.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                        );
                    }
    
                    //put the values from this iteration into the output
                    $tmpStmt[] = $tmp['stmt'];
                    foreach ($tmp['args'] as $arg) {
                        $outArray['args'][] = $arg;
                    }
                }
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'() failed due to input of type other than array.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }
    
        //combine statement segments into a singular statement string
        if ($condType == 'AND') {
            $outArray['stmt'] = implode(' AND ', $tmpStmt);
        } elseif ($condType == 'OR') {
            $outArray['stmt'] = implode(' OR ', $tmpStmt);
        } elseif ($condType == 'XOR') {
            $outArray['stmt'] = implode(' XOR ', $tmpStmt);
        } elseif ($condType == 'NONE') {
            $outArray['stmt'] = '';
            $outArray['args'] = [];
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'() failed due to an invalid boolean block.',
                DatabaseException::EXCEPTION_INPUT_NOT_VALID
            );
        }
    
        //encapsulate the statement/segment if necessary
        if ($encap) {
            $outArray['stmt'] = '('.$outArray['stmt'].')';
        }
    
        //return stuff
        return $outArray;
    
    }

}

?>
