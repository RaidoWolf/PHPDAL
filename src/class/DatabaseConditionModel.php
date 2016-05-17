<?php

class DatabaseConditionModel implements DatabaseConditionInterface {

    /**
     * DatabaseConditionModel Constructor
     *
     * The idea for the new DatabaseConditionModel is that the objects should
     * represent the lowest layer of a database condition. Thus, an instance of
     * DatabaseConditionModel should contain three things, the name of the property
     * to match, the value of the property to match, and the relation of the name and
     * the property (equals, not-equals, greater-than, in, etc.)
     *
     * However, to maximize interoperability, the next layer of condition objects,
     * which will act as a container and relationship manager for instances of
     * DatabaseConditionModel when used to provide filtering for DatabaseModel
     * instances, must extend this class so that it can not only be matched by a call
     * to is_subclass_of() or a type declaration in a function parameter.
     * Additionally, however, extending this class will also provide the basis for a
     * common interface to exist for all representations of database conditions.
     */
    public function __construct () {

        //TODO

    }

    protected function getToken ($key) {

        return DatabaseGrammarModel::getToken($key);

    }

}

class DatabaseConditionModelOld implements DatabaseConditionInterfaceOld {

    protected $parent = null;
    protected $statement = [];
    protected $structure = [];
    protected $table = null;

    /**
     * Constructor Method
     * @param unknown $struct
     */
    public function __construct ($struct = [], &$parent, $table) {

        $this->structure = $struct; //store the structure
        $this->parent = &$parent; //store the parenterence
        $this->table = $table; //store the table context
        $this->statement = $this->parse($struct); //generate and store the statement and parameters

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

        return serialize($this);

    }

    /**
     * DatabaseConditionModel->add() Method
     *
     * Add (array_merge) a condition structure to the existing condition structure.
     *
     * @throws DatabaseException if you dun goof
     * @see DatabaseConditionInterface::add()
     */
    public function add ($rule) {

        $typeof_rule = gettype($rule);
        $typeof_structure = gettype($this->structure);
        if ($typeof_rule == 'array') {
            //add the rule(s)
            if ($typeof_structure == 'array') {

                //handle wildcard weirdness
                if ($rule == ['*'] || $rule == [['*']]) {
                    $this->structure = ['*'];
                    $this->statement = $this->parse($this->structure);
                } else {
                    if ($this->structure == ['*'] || $this->structure == [['*']]) {
                        $this->structure = [];
                    }
                }

                //merge the arrays and generate statement string
                $this->structure = array_unique(array_merge($rule, $this->structure));
                $this->statement = $this->parse($this->structure);
            } else {
                throw new DatabaseException(
                    __METHOD__.'(): structure array of invalid type encountered as structure object.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                    $this
                );
            }
        } else {
            throw new DatabaseException(
                __METHOD__.'(): structure array of invalid type given as rule to add.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                $this
            );
        }

    }

    /**
     * DatabaseConditionModel->del() Method
     *
     * Delete (array_diff) a condition structure from existing condition structure.
     *
     * @throws DatabaseException if you dun goof
     * @see DatabaseConditionInterface::del()
     */
    public function del ($rule) {

        if (is_array($rule) && is_array($this->structure)) {
            $this->structure = array_diff($this->structure, $rule);
        } else {
            if (!is_array($rule)) {
                throw new DatabaseException(
                    __METHOD__.'(): encountered structure array of invalid type given as argument.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                    $this
                );
            }
            if (!is_array($this->structure)) {
                throw new DatabaseException(
                    __METHOD__.'(): encountered structure array of invalid type stored in object.',
                    DatabaseException::EXCEPTION_CORRUPTED_OBJECT,
                    $this
                );
            }
        }

    }

    public function getStatement () {

        return $this->statement;

    }

    public function getStructure () {

        return $this->structure;

    }

    protected function getToken ($key) {

        return DatabaseGrammarMode::getToken($key);

    }

    /**
     * DatabaseConditionModel->parse() Method
     *
     * Converts an associative array of SQL boolean logic conditions to a string
     * that can be used in a standard SQL query.
     *
     * @param array $array
     * @param bool $encap
     * @throws DatabaseException if you dun goof
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
     * The folliowing is an example condition structure. This must be passed as a PHP array.
     *
     *  [
     *      [
     *          'comparator' => 'OR',
     *          'contents' => [
     *              [
     *                  'type'  => '>',
     *                  'key'   => 'id',
     *                  'value' => 1924
     *              ],
     *              [
     *                  'comparator' => 'AND',
     *                  'contents' => [
     *                      [
     *                          'type'  => '<>',
     *                          'key'   => 'pos',
     *                          'lower' => 100,
     *                          'upper' => 1000
     *                      ],
     *                      [
     *                          'type'  => '![]',
     *                          'key'   => 'type',
     *                          'set'   => [
     *                              'primary',
     *                              'secondary,
     *                              'backup'
     *                          ]
     *                      ],
     *                      [
     *                          'type'  => '=',
     *                          'key'   => 'active',
     *                          'value' => 1
     *                      ]
     *                  ]
     *              ]
     *          ]
     *      ]
     *  ]
     *
     *  This will produce the following SQL statement (in standard SQL):
     *  "id" > 1924 OR ("pos" BETWEEN 100 AND 1000 AND "type" in ('primary', 'secondary', 'backup') AND "active" = 1)
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
                $comparator = 'NONE';
                $tmpStmt = [];
            } else {
                //array has conditions (or at least content)
                foreach ($array as $value) {

                    $comparator = 'AND'; //default and first-level comparator default to 'AND'
                    $tmp = []; //instantiate temporary statement (segment) array
                    $typeof_value = gettype($value); //store the data type of $value

                    if ($typeof_value == 'array') {
                        if (array_key_exists('comparator', $value)) {
                            //boolean object encountered
                            $comparator = $value['comparator'];
                            if (!in_array($comparator, ['AND', 'OR', 'XOR'])) {
                                throw new DatabaseException(
                                    __METHOD__.'(): invalid comparator encountered.',
                                    DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                                    $this
                                );
                                continue; //skip this iteration (in case exception is caught)
                            }
                            if (array_key_exists('contents', $value)) {
                                $tmp = $this->parse($value['contents'], true);
                            } else {
                                throw new DatabaseException(
                                    __METHOD__.'(): missing contents key in array.',
                                    DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                                    $this
                                );
                                continue; //skip this iteration (in case exception is caught)
                            }
                        } elseif (array_key_exists('type', $value)) {
                            //comparison object encountered
                            $type = $value['type'];

                            //make sure all given values are scalar
                            foreach ($value as $data) {
                                if (!is_scalar($data)) {
                                    throw new DatabaseException(
                                            __METHOD__.'(): encountered non-scalar data.'
                                    );
                                }
                                continue 2; //skip this iteration on outer loop (in case exception is caught)
                            }

                            //if a set is given, convert it into a single string
                            if (isset($value['set'])) {
                                $value['setstring'] = implode($this->getToken('setDelimiter'), $value['set']);
                            }

                            //parse operations structures
                            if          ($type == '='       || $type == 'EQ')       {
                                $grammar = $this->getToken('op_eq');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!'       || $type == 'NOT')      {
                                $grammar = $this->getToken('op_not');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<'       || $type == 'LT')       {
                                $grammar = $this->getToken('op_lt');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<='      || $type == 'LTE')      {
                                $grammar = $this->getToken('op_lte');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '>'       || $type == 'GT')       {
                                $grammar = $this->getToken('op_gt');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '>='      || $type == 'GTE')      {
                                $grammar = $this->getToken('op_gte');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<>'      || $type == 'RANGE')    {
                                $grammar = $this->getToken('op_range');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '<x>'     || $type == 'XRANGE')   {
                                $grammar = $this->getToken('op_xrange');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!<>'     || $type == 'NRANGE')   {
                                $grammar = $this->getToken('op_nrange');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => [],
                                ];
                            } elseif    ($type == '!<x>'    || $type == 'NXRANGE')  {
                                $grammar = $this->getToken('op_nxrange');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '[]'      || $type == 'IN')       {
                                //TODO: escape each value in the set separately
                                $grammar = $this->getToken('op_in');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '![]'     || $type == 'NIN')      {
                                //TODO: escape each value in the set separately
                                $grammar = $this->getToken('op_nin');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '~'       || $type == 'LIKE')     {
                                $grammar = $this->getToken('op_like');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!~'      || $type == 'NLIKE')    {
                                $grammar = $this->getToken('op_nlike');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == ':0'      || $type == 'ISNULL')   {
                                $grammar = $this->getToken('op_isnull');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } elseif    ($type == '!:0'     || $type == 'NISNULL')  {
                                $grammar = $this->getToken('op_nisnull');
                                $tmp = [
                                    'stmt' => $grammar['stmt'],
                                    'args' => []
                                ];
                            } else {
                                //given comparison type invalid
                                throw new DatabaseException(
                                    __METHOD__.'(): encountered invalid comparison type.',
                                    DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                                    $this
                                );
                                continue; //skip this iteration (in case exception is caught)
                            }

                            //insert all arguments into $tmp['args']
                            foreach ($grammar['args'] as $arg) {
                                if (!isset($value[$arg])) {
                                    throw new DatabaseException(
                                        __METHOD__.'(): encountered a missing required argument in the structure.',
                                        DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                                        $this
                                    );
                                    $tmp['args'][] = null; //add null filler to arguments array (in case exception is caught)
                                    continue; //skip this iteration (in case exception is caught)
                                }
                                $tmp['args'][] = $value[$arg];
                            }
                        } else {
                            throw new DatabaseException(
                                __METHOD__.'(): encountered array object with no comparator or type argument',
                                DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                                $this
                            );
                            continue; //skip this iteration (in case exception is caught)
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
                            __METHOD__.'(): encountered block of invalid type.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                            $this
                        );
                        continue; //skip this iteration (in case exception is caught)
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
                __METHOD__.'(): encountered input of type other than array.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                $this
            );
            return false; //indicate failure (in case exception is caught)
        }

        //combine statement segments into a singular statement string
        if ($comparator == 'AND') {
            $outArray['stmt'] = implode($this->getToken('and'), $tmpStmt);
        } elseif ($comparator == 'OR') {
            $outArray['stmt'] = implode($this->getToken('or'), $tmpStmt);
        } elseif ($comparator == 'XOR') {
            $outArray['stmt'] = implode($this->getToken('xor'), $tmpStmt);
        } elseif ($comparator == 'NONE') {
            $outArray['stmt'] = '';
            $outArray['args'] = [];
        } else {
            throw new DatabaseException(
                __METHOD__.'(): encountered invalid boolean block.',
                DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                $this
            );
            return false; //indicate failure (in case exception is caught)
        }

        //encapsulate the statement/segment if necessary
        if ($encap) {
            $outArray['stmt'] = $this->getToken('encapLeft').$outArray['stmt'].$this->getToken('encapRight');
        }

        //return stuff
        return $outArray;

    }

}

?>
