<?php

/**
 * DatabaseStatement Class
 * =======================
 * Class to contain, represent, and provide interface for PDO prepared
 * statements.
 * @package PHPDAL
 * @author Alexander Barber
 */
class DatabaseStatement {

    //Prepared Statement Input Types
    const TYPE_BOOL             = PDO::PARAM_BOOL;          //boolean data type
    const TYPE_INT              = PDO::PARAM_INT;           //integer data type
    const TYPE_LOB              = PDO::PARAM_LOB;           //large object data type
    const TYPE_NULL             = PDO::PARAM_NULL;          //null data type
    const TYPE_OUTPUT           = PDO::PARAM_INPUT_OUTPUT;  //INOUT parameter for stored procedure (must be bitwise-OR'd with another data type)
    const TYPE_STMT             = PDO::PARAM_STMT;          //recordset type (not supported at the moment)
    const TYPE_STR              = PDO::PARAM_STR;           //string data type

    //Member Variables
    protected $connector;   //PDO Connection from parent object.
    protected $count;       //Count of arguments (to verify all database parameters are given).
    protected $executions;  //Count of times this prepared statement was executed.
    protected $parent;      //Parent object (the object that created this object).
    protected $query;       //Original query string, which was prepared.
    protected $signature;   //For comparing existing statements and optimizing database operations.
    protected $stmt;        //PDOStatement Object.

    /**
     * DatabaseStatement->__construct() Method
     *
     * Constructor for DatabaseStatement.
     *
     * @param  DatabaseModel $parent Calling object (inherited from DatabaseModel)
     * @param  string        $query  Query string to prepare.
     * @param  array         $args   Array of argument types to prepare with it.
     * @throws DatabaseException     If you dun goof.
     */
    public function __construct (
        $parent,
        $query,
        $args = []
    ) {

        //set variables
        $this->count        = substr_count($query, '?');    //set argument count
        $this->connector    = $connector;                   //set connector (will be an object reference)
        $this->parent       = $parent;                      //set parent object (will be an object reference)
        $this->query        = $query;                       //set query string
        //TODO: Revisit this signature property concept. Is it needed? Should this be SHA256 instead? MD5 has known collisions.
        $this->signature    = md5($query);                  //create and set the MD5 signature

        //prepare statement
        $this->stmt         = $this->connector->prepare($this->query); //create and set PDO prepared statement object

    }

    /**
     * DatabaseStatement->__invoke() Method
     *
     * Invocation method for DatabaseStatement.
     * This is just an alias method for exec().
     *
     * @param  array             $args Array of values to bind and execute.
     * @return array|boolean           Array of fetched values, or false on failure.
     * @throws DatabaseException       If you dun goof.
     */
    public function __invoke ($args) {

        $this->exec($args);

    }

    /**
     * DatabaseStatement->exec() Method
     *
     * Bind and execute statement for given values.
     *
     * @param  array             $args Array of values to bind and execute.
     * @return array|boolean           Array of fetched values, or false on failure.
     * @throws DatabaseException       If you dun goof.
     */
    public function exec ($args = []) {

        if (is_array($args)) { //make sure $args is an array
            if (count($args) == $this->count) { //make sure number of arguments matches
                $i = 1; //instantiate param index counter at 1
                foreach ($args as $arg) { //loop for each argument
                    if (is_array($arg)) { //check if argument is an array
                        if (isset($arg['value'])) { //check if argument has a value
                            //bind the parameter
                            $this->stmt->bindParam(
                                $i,
                                $arg['value'],
                                isset($arg['type']) ? $arg['type'] : self::TYPE_STR //if has custom type, use it.
                            );
                            $i++; //increment param index
                        } else {
                            //array argument has no applicable value
                            throw new DatabaseException(
                                $this,
                                __METHOD__.'(): encountered array in parameter array without "value" key.',
                                DatabaseException::EXCEPTION_INPUT_NOT_VALID
                            );
                            continue; //skip this iteration (if exception was caught)
                        }
                    } else { //argument is not an array
                        $data = $this->paramPrepareType($arg); //determine appropriate PDO parameter type (and serialize if needed)
                        if (!$data) { //if we can't determine the type
                            //unable to determine applicable data type.
                            throw new DatabaseException(
                                $this,
                                __METHOD__.'(): encountered parameter of unknown type.',
                                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                            );
                            //give it your best shot (in case exception is caught)
                            $data = []; //instantiate data array
                            $data['value'] = $arg; //put whatever $arg is into 'value' key
                            $data['type'] = self::TYPE_STR; //and try it as a string
                        }
                        //bind the parameter
                        $this->stmt->bindParam(
                            $i,
                            $data['value'],
                            $data['type']
                        );
                        $i++; //increment param index
                    }
                }
            } else {
                //$args variable count mismatch
                throw new DatabaseException(
                    $this,
                    __METHOD__.'(): incorrect number of values in arguments array.',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
                return false; //in case exception is caught
            }
        } else {
            //$args type violation
            throw new DatabaseException(
                $this,
                __METHOD__.'(): encountered input of invalid type.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
            return false; //in case exception is caught
        }

        try {
            $this->stmt->exec(); //try to execute the statement.
        } catch (PDOException $e) { //catch any exceptions and...
            //PDO throws exception
            throw new DatabaseException( //throw our own.
                $this,
                __METHOD__.'(): caught exception thrown by PDO during statement execution.',
                DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                $e
            );
            return false; //in case exception is caught
        }
        $this->executions++; //increment execution counter

    }

    /**
     * DatabaseStatement->getArgCount() Method
     *
     * Get the valid number of arguments for the statement.
     * All exec() calls must have this number of parameters, or an exception
     * will be thrown.
     *
     * @return int Number of arguments given during statement construction.
     */
    public function getArgCount () {

        return $this->count;

    }

    /**
     * DatabaseStatement->getConnector() Method
     *
     * Get the parent's database connector object.
     *
     * @return PDO Parent's database connector object.
     */
    public function getConnector () {

        return $this->connector;

    }

    /**
     * DatabaseStatement->getExecCount() Method
     *
     * Get the number of times the statement has been executed.
     *
     * @return int Number of times the statement has been executed.
     */
    public function getExecCount () {

        return $this->executions;

    }

    /**
     * DatabaseStatement->getParent() Method
     *
     * Get the parent (calling) object.
     *
     * @return DatabaseModel Parent (calling) object (inherited from DatabaseModel).
     */
    public function getParent () {

        return $this->parent;

    }

    /**
     * DatabaseStatement->getQuery() Method
     *
     * Get the query string that was originally prepared.
     *
     * @return string Query string given to the constructor.
     */
    public function getQuery () {

        return $this->query;

    }

    /**
     * DatabaseStatement->getSignature() Method
     *
     * Get the MD5 checksum of the statement string after being prepared. This
     * is meant for automated optimization by checking against existing
     * prepared statements and preventing re-preparing statements that can
     * simply be executed with the given data.
     *
     * //TODO: This might've been a misguided attempt at this concept, check
     * //TODO: Maybe don't run MD5 until this is called?
     *
     * @return string MD5 checksum of the statement string.
     */
    public function getSignature () {

        return $this->signature;

    }

    /**
     * DatabaseStatement->getStmt() Method
     *
     * Get the PDOStatement object containing the actual prepared statement.
     *
     * @return PDOStatement Prepared statement object.
     */
    public function getStmt () {

        return $this->stmt;

    }

    /**
     * DatabaseStatement->paramPrepareType() Method
     *
     * Calculate the appropriate data type for which to execute a given prepared
     * statement argument.
     *
     * @param  unknown       $arg Any type of intended input data.
     * @return array|boolean Array with 'value' and 'type' keys, or false if it fails.
     */
    protected function paramPrepareType ($arg) {

        switch (gettype($arg)) {
            //boolean type
            case 'boolean':
                $type = self::TYPE_BOOL;
                break;
            //integer type
            case 'integer':
                $type = self::TYPE_INT;
                break;
            //double/float type
            case 'double':
                $type = self::TYPE_STR;
                break;
            //string type
            case 'string':
                $type = self::TYPE_STR;
                break;
            //array type
            case 'array':
                $type = self::TYPE_STR;
                $arg = serialize($arg);
                break;
            //object type
            case 'object':
                $type = self::TYPE_STR;
                $arg = serialize($arg);
                break;
            //resource type (not valid for storage)
            case 'resource':
                return false;
                break;
            //null type
            case 'NULL':
                $type = self::TYPE_NULL;
                break;
            //unknown type
            case 'unknown type':
                return false;
                break;
            //also unknown, but not explicitly unknown (should never happen)
            default:
                return false;
                break;
        }

        //build output array (because the argument may sometimes have to be processed to be valid)
        $out = [
            'value' => $arg,
            'type' => $type
        ];

        //return output
        return $out;

    }

}

?>
