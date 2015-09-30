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

    public function __construct (
        $parent,
        $query,
        $args = []
    ) {

        //set variables
        $this->count = substr_count($query, '?');   //set argument count
        $this->connector = $connector;              //set connector (will be an object reference)
        $this->parent = $parent;                    //set parent object (will be an object reference)
        $this->query = $query;                      //set query string
        $this->signature = md5($query);             //create and set the MD5 signature

        //prepare statement
        $this->stmt = $this->connector->prepare($this->query); //create and set PDO prepared statement object

    }

    public function __invoke ($args) {

        $this->exec($args);

    }

    public function exec ($args) {

        if (!isset($args) || $args == null) {
            $args = [];
        }

        if (is_array($args)) {
            if (count($args) == $this->count) {
                $i = 1;
                foreach ($args as $arg) {
                    if (is_array($arg)) {
                        if (isset($arg['value'])) {
                            $this->stmt->bindParam(
                                $i,
                                $arg['value'],
                                isset($arg['type']) ? $arg['type'] : self::TYPE_STR
                            );
                            $i++;
                        } else {
                            throw new DatabaseException(
                                $this,
                                __CLASS__.'->'.__METHOD__.'(): encountered array in parameter array without "value" key.',
                                DatabaseException::EXCEPTION_INPUT_NOT_VALID
                            );
                            continue; //skip this iteration (if exception was caught)
                        }
                    } else {
                        $data = $this->paramPrepareType($arg);
                        if (!$data) {
                            throw new DatabaseException(
                                $this,
                                __CLASS__.'->'.__METHOD__.'(): encountered parameter of unknown type.',
                                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                            );
                            //give it your best shot (in case exception is caught)
                            $data = [];
                            $data['value'] = $data;
                            $data['type'] = self::TYPE_STR;
                        }
                        $this->stmt->bindParam(
                            $i,
                            $data['value'],
                            $data['type']
                        );
                        $i++;
                    }
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): incorrect number of values in arguments array.',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
                return false; //in case exception is caught
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): encountered input of invalid type.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
            return false; //in case exception is caught
        }

        try {
            $this->stmt->exec(); //try to execute the statement.
        } catch (PDOException $e) { //catch any exceptions and...
            throw new DatabaseException( //throw our own.
                $this,
                __CLASS__.'->'.__METHOD__.'(): caught exception thrown by PDO during statement execution.',
                DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                $e
            );
            return false; //in case exception is caught
        }
        $this->executions++; //increment execution counter

    }

    public function getArgCount () {

        return $this->count;

    }

    public function getQuery () {

        return $this->query;

    }

    public function getSignature () {

        return $this->signature;

    }

    public function getStmt () {

        return $this->stmt;

    }

    protected function paramPrepareType ($arg) {

        switch (gettype($arg)) {
            case 'boolean':
                $type = self::TYPE_BOOL;
                break;
            case 'integer':
                $type = self::TYPE_INT;
                break;
            case 'double':
                $type = self::TYPE_STR;
                break;
            case 'string':
                $type = self::TYPE_STR;
                break;
            case 'array':
                $type = self::TYPE_STR;
                $arg = serialize($arg);
                break;
            case 'object':
                $type = self::TYPE_STR;
                $arg = serialize($arg);
                break;
            case 'resource':
                return false;
                break;
            case 'NULL':
                $type = self::TYPE_NULL;
                break;
            case 'unknown type':
                return false;
                break;
            default:
                $type = self::TYPE_STR;
                break;
        }

        $out = [
            'value' => $arg,
            'type' => $type
        ];

        return $out;

    }

}

?>
