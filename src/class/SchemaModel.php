<?php

class SchemaModel implements SchemaInterface {

    const TYPE_NONE         = 000; //This literally does nothing. Don't use it.
    const TYPE_BIGINT       = 001; //Value representing an integer up to 64 bits.
    const TYPE_BIT          = 002; //Value representing one bit, can only be true or false.
    const TYPE_BLOB         = 003; //Value representing off-table binary data up to 65KiB (Binary Large OBject).
    const TYPE_BYTE         = 004; //Value representing one byte.
    const TYPE_CHAR         = 005; //Value representing a fixed-length string up to 255 characters (requires length definition).
    const TYPE_DATE         = 006; //Value representing a date.
    const TYPE_DATETIME     = 007; //Value representing date and time.
    const TYPE_DECIMAL      = 008; //Value representing a floating point number stored as a string, eliminating rounding errors (requires length and decimal defintions).
    const TYPE_DOUBLE       = 009; //Value representing a 64-bit floating point number (requires length and decimal definitions).
    const TYPE_ENUM         = 010; //Value representing a value from a list of up to 65535 values (requires enum definition).
    const TYPE_FLOAT        = 011; //Value representing a 32-bit floating point number (requires length and decimal definitions).
    const TYPE_INT          = 012; //Value representing an integer up to 32 bits.
    const TYPE_LONGBLOB     = 013; //Value representing off-table binary data up to 4GiB (Binary Large OBject).
    const TYPE_LONGTEXT     = 014; //Value representing off-table string up to 4,294,967,295 characters.
    const TYPE_MEDIUMBLOB   = 015; //Value representing off-table binary data up to 16MiB (Binary Large OBject).
    const TYPE_MEDIUMINT    = 016; //Value representing an integer up to 24 bits.
    const TYPE_MEDIUMTEXT   = 017; //Value representing off-table string up to 16,777,215 characters.
    const TYPE_SET          = 018; //Value representing one or more values from a list of up to 64 values (requires enum definition).
    const TYPE_SMALLINT     = 019; //Value representing integers up to 16 bits.
    const TYPE_TEXT         = 020; //Value representing off-table string up to 65,535 characters.
    const TYPE_TIME         = 021; //Value representing a time.
    const TYPE_TIMESTAMP    = 022; //Value representing a Unix timestamp.
    const TYPE_TINYINT      = 023; //Value representing an integer up to 8 bits.
    const TYPE_TINYTEXT     = 024; //Value representing off-table string up to 255 characters.
    const TYPE_VARCHAR      = 025; //Value representing variable length string up to 255 characters (requires length definition). Length over 255 will convert to TYPE_TEXT.
    const TYPE_YEAR         = 026; //Value representing a year in two digits (70-69 as 1970-2069) or four digits (1901-2155).

    protected $database;
    protected $databaseValid;

    protected $schemas = [
        'tables'    => [],
        'views'     => [],
        'triggers'  => []
    ];

    public function __construct () {

        //TODO: Implement constructor of SchemaModel.

    }

    public function __invoke () {

        //TODO: Implement invocation method of SchemaModel.

    }

    public function __toString () {

        //TODO: Implement string conversion method of SchemaModel.

    }

    public function columnConform ($table, $column) {

        if ($this->isBound()) {
            if (!$this->columnMatches($table, $column)) {
                //TODO: Implement alter column to match the schema in the object.
            } else {
                return true; //schema already conforms
            }
        } else {
            throw new DatabaseException(
                $this,
                __METHOD__.'(): Not bound to a Database object.'
            );
        }

    }

    public function columnMatches ($table, $column) {

        //TODO: Implement check if schema for given column in object matches actual schema.

    }

    public function getAllSchemas () {

        return $this->schemas;

    }

    public function getCreateQuery () {

        //TODO: Implement query string generator for schemas.

    }

    public function getSchema ($name) {

        //TODO: Implement schema lookup function.

    }

    public function handleValueIn ($table, $key, $value) {

        //TODO: Implement DBMS input feature emulation.

    }

    public function handleValueOut ($table, $key, $value) {

        //TODO: Implement DBMS output feature emulation.

    }

    public function schemaConform () {

        if ($this->validateDatabaseObject()) {
            if (!$this->schemaMatches()) {
                if ($this->schemaMatches()) {
                    return true; //schema already matches
                } else {
                    $output = true;

                    //conform the existing tables
                    foreach ($this->tables as $table) {
                        $thisResult = $this->tableConform($table);
                        if (!$thisResult) {
                            $output = false;
                        }
                    }

                    //conform the existing views
                    foreach ($this->views as $view) {
                        $thisResult = $this->viewConform($view);
                        if (!$thisResult) {
                            $output = false;
                        }
                    }

                    //conform the existing triggers
                    foreach ($this->triggers as $trigger) {
                        $thisResult = $this->triggerConform($trigger);
                        if (!$thisResult) {
                            $output = false;
                        }
                    }

                    return $output;
                }
            } else {
                return true; //schema already conforms.
            }
        } else {
            return false;
        }

    }

    public function schemaMatches () {

        //TODO: Implement check if schema object matches actual schema.

    }

    public function tableConform ($table) {

        if ($this->validateDatabaseObject()) {
            if (!$this->tableMatches($table)) {
                if ($this->tableMatches($table)) {
                    return true; //table already matches
                } else {
                    //TODO: Figure this out
                }
            } else {
                return true; //table already conforms
            }
        } else {
            return false;
        }

    }

    public function tableMatches ($table) {

        //TODO: Implement check if schema for given table in object matches actual schema.

    }

    public function triggerConform ($trigger) {

        if ($this->validateDatabaseObject()) {
            if (!$this->tableMatches($trigger)) {
                if ($this->triggerMatches($trigger)) {
                    return true; //trigger already matches
                } else {
                    //TODO: Figure this out
                }
            } else {
                return true; //trigger already conforms
            }
        } else {
            return false;
        }

    }

    public function triggerMatches ($trigger) {

        //TODO: Implement trigger conformity checker.

    }

    protected function validateDatabaseObject () {

        if (isset($this->databaseValid) && $this->databaseValid != null) {
            return $this->databaseValid;
        } else {
            if (!isset($this->database) || $this->database == null) {
                if (is_object($this->database)) {
                    if (is_subclass_of($this->database, 'Database')) {
                        $this->databaseValid = true;
                        return true;
                    } else {
                        throw new DatabaseException(
                            $this,
                            __METHOD__.'(): Database connection must be an object descended of class Database.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                        );
                        $this->databaseValid = false;
                        return false;
                    }
                } else {
                    throw new DatabaseException(
                        $this,
                        __METHOD__.'(): Database connection must be an object descended of class Database.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                    $this->databaseValid = false;
                    return false;
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __METHOD__.'(): Not bount to a Database object.',
                    DatabaseException::EXCEPTION_MISSING_CONNECTION
                );
                $this->databaseValid = false;
                return false;
            }
        }

    }

    public function viewConform ($view) {

        //TODO: Implement view conformity forcer.

    }

    public function viewMatches ($view) {

        //TODO: Implement view conformity checker.

    }

}

?>
