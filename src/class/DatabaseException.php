<?php

class DatabaseException extends Exception implements DatabaseExceptionInterface {

    protected $caller;
    protected $caught;

    // -- ERROR CODES -- //

    // -- 0000 - Basic Errors -- //
    const EXCEPTION_NO_ERROR                    =    0; //No error has occurred.
    const EXCEPTION_TEST                        =    1; //Test exception. Should never happen in production code.
    const EXCEPTION_GENERIC_ERROR               =    2; //Some error has occurred.
    const EXCEPTION_INVALID_EXCEPTION           =    3; //The caught exception passed to this class was not a valid Exception object. Usually means it isn't an exception at all.
    const EXCEPTION_CORRUPTED_OBJECT            =    4; //The data stored in the object is not valid, and therefore the object is corrupted.
    const EXCEPTION_STACK_OVERFLOW              =    5; //An explicitly stack-depth-limited recursive function exceeded the maximum stack depth.
    // -- 1000 - Input Errors -- //
    const EXCEPTION_GENERIC_INPUT_ERROR         = 1000; //Some issue with input data.
    const EXCEPTION_INPUT_INVALID_TYPE          = 1001; //Input data of an invalid type. Usually means that one or more input values could only accept a specific type of data, but was given a different type of data.
    const EXCEPTION_INPUT_NOT_VALID             = 1002; //Data entered that was not an acceptable value. Generally implies there was a limited set of acceptable values and the given value was not among them.
    const EXCEPTION_MISSING_REQUIRED_ARGUMENT   = 1003; //Missing argument where it was required by the function or method. Usually means that the function or method was used incorrectly, or you forgot something.
    const EXCEPTION_MISSING_DEFINITION          = 1004; //Missing definition/configuration where it was required. Usually means that an optional parameter to fallback to a pre-configured value failed because the pre-configured value was missing.
    const EXCEPTION_INPUT_ARRAY_TOO_DEEP        = 1005; //An input array is too deep to process. This usually indicates that either the input structure was misunderstood, or that some data needs to be serialized.
    const EXCEPTION_INPUT_ARRAY_TOO_SHALLOW     = 1006; //An input array is too shallow to process. This usually indicates that either the input structure was misunderstood, or that some data was serialized where it should not have been.
    const EXCEPTION_MISSING_CONNECTION          = 1007; //An inter-object connection (typically a reference stored somewhere) crucial to the function has not been established.
    const EXCEPTION_USING_RESERVED_KEYWORD      = 1008; //A value in input (usually parameter passed in an array) is using a defined reserved keyword.
    const EXCEPTION_ALREADY_EXISTS              = 1009; //A value in the input meant to be a unique identifier is using the same identifier as a previously-existing definition.
    // -- 2000 - Database Errors -- //
    const EXCEPTION_GENERIC_DATABASE_ERROR      = 2000; //Some issue with the database or database driver.
    const EXCEPTION_DB_ITEM_CANNOT_BE_CREATED   = 2001; //A database item cannot be created.
    const EXCEPTION_DB_ITEM_CANNOT_BE_DROPPED   = 2002; //A database item cannot be destroyed.
    const EXCEPTION_DB_ITEM_ALREADY_EXISTS      = 2003; //A database item cannot be created because it already exists.
    const EXCEPTION_DB_ITEM_DOES_NOT_EXIST      = 2004; //A database item cannot be destroyed because it does not exist.
    const EXCEPTION_DB_OPERATION_LOCKED         = 2005; //The database operation failed because of a database lock.
    const EXCEPTION_DB_OPERATION_READ_ONLY      = 2006; //The database operation failed because the database or table was read only.
    const EXCEPTION_DB_CANNOT_READ_RECORD       = 2007; //The database was unable to read a record of an unspecified type.
    const EXCEPTION_DB_CANNOT_GET_STATUS        = 2008; //The database is unable to determine an unspecified status.
    const EXCEPTION_DB_RECORD_INCONSISTENCY     = 2009; //The database engine detected that a record has been changed since the last time it was read (potential data corruption).
    // -- 3000 - System Errors -- //
    const EXCEPTION_GENERIC_SYSTEM_ERROR        = 3000; //Some issue with the operating system, kernel, or system-level components (such as the filesystem).
    const EXCEPTION_FS_CANNOT_OPEN_FILE         = 3001; //The database engine was unable to open the file for an unspecified reason.
    const EXCEPTION_FS_CANNOT_ACCESS_DIR        = 3002; //The database engine was unable to access a directory.
    const EXCEPTION_FS_CANNOT_CREATE_FILE       = 3003; //The filesystem was unable to create a file for an unspecified reason.
    const EXCEPTION_FS_CANNOT_DELETE_FILE       = 3004; //The filesystem was unable to delete a file for an unspecified reason.
    const EXCEPTION_FS_FILE_NOT_FOUND           = 3005; //The filesystem was unable to find the file at the specified location. (like HTTP 404)
    const EXCEPTION_FS_OUT_OF_SPACE             = 3006; //The filesystem on which the database resides is out of space.
    const EXCEPTION_FS_PERMISSIONS_ERROR        = 3007; //The filesystem permissions caused the database operation to fail.
    const EXCEPTION_FS_CANNOT_GET_CWD           = 3008; //The filesystem failed to provide the current working directory.
    const EXCEPTION_FS_CANNOT_LOCK_FILE         = 3009; //The filesystem failed to lock the file for the database engine.
    const EXCEPTION_FS_CANNOT_CHANGE_DIR        = 3010; //The database engine was unable to change directories for an unspecified reason.
    // -- 4000 - Network Errors -- //
    const EXCEPTION_GENERIC_NETWORK_ERROR       = 4000; //Some issue with the network stack, hardware, software, or infrastructure.
    const EXCEPTION_NET_TIMEOUT                 = 4001; //Connection to the database server timed out.
    const EXCEPTION_NET_RESET_BY_PEER           = 4002; //Connection reset by peer.
    const EXCEPTION_NET_CONNECTION_REFUSED      = 4003; //Connection refused.

    // -- 10000+ - Reserved for DBMS-Specific Exceptions -- //

    /**
     * Constructor Method
     *
     * @param $parent (required) -
     */
    public function __construct (
            &$caller,
            $message = null,
            $code = null,
            &$caught = null
    ) {

        //$caught input handler
        if ($caught != null) {
            if (!is_subclass_of($caught, 'Exception')) {
                throw new self(
                        $this->getCaller(),
                        'DatabaseException->__construct() failed due to caught exception argument not descended from class Exception.',
                        self::EXCEPTION_INVALID_EXCEPTION,
                        $this
                );
            }
        }

        //call Exception class's constructor now
        parent::__construct($message, $code, $caught);

    }

    public function &getCaller () {

        return $this->caller;

    }

    /**
     * DatabaseException->getConstants() protected Method
     *
     * gets an array of constants for the DatabaseException object
     *
     * @return array - Array of constants as constantName => constantValue
     */
    public function getConstants () {

        $refl = new ReflectionClass(__CLASS__);
        return $refl->getConstants();

    }

    /**
     * DatabaseException->getErrorFlag() Method
     *
     * gets the flag (constant) name of the exception's error type
     *
     * @return string - Name of flag of the exception's error type
     */
    public function getErrorFlag () {

        $constArray = $this->getConstants(); //get array of constants
        $error = $this->error; //get error number
        foreach ($constArray as $constName => $constValue) { //loop each constant
            if ($constValue == $error && preg_match('/^EXCEPTION_.*$/', $constName)) { //if error number matches constant value and has prefix EXCEPTION_
                return 'DatabaseException::'.$constName; //return constant name (and end function)
            }
        }
        return false; //return false if there are no matches

    }

}

?>
