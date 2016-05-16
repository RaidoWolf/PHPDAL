<?php

class Schema implements SchemaInterface {

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

    protected $tables = [];
    protected $views = [];
    protected $triggers = [];

    public function __construct ($items) {

        if (is_array($items)) {
            foreach ($items as $itemKey => $item) {
                if (is_subclass_of($item, 'SchemaComponentModel')) {
                    if (is_subclass_of($item, 'SchemaTable')) {
                        $this->addTable($item, $itemKey);
                    } elseif (is_subclass_of($item, 'SchemaView')) {
                        $this->addView($item, $itemKey);
                    } elseif (is_subclass_of($item, 'SchemaTrigger')) {
                        $this->addTrigger($item, $itemKey);
                    } else {
                        throw new DatabaseException(
                            $this,
                            __METHOD__.'(): Array element ['.$itemKey.'] is descended from neither SchemaTable, SchemaView, or SchemaTrigger.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                        );
                    }
                } else {
                    throw new DatabaseException(
                        $this,
                        __METHOD__.'(): Array element ['.$itemKey.'] is not a descendant of class SchemaComponentModel.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
            }
        } else {
            throw new DatabaseException(
                $this,
                __METHOD__.'(): Argument must be an array of SchemaComponentModel-descended objects.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

    }

    public function addTable ($table, $name) {

        if (is_subclass_of($table, 'SchemaTable')) {
            if ($this->nameAvailable($name)) {
                $this->tables[$name] = $table;
            } else {
                throw new DatabaseException(
                    $this,
                    __METHOD__.'(): Name "'.$name.'" already exists in this schema.',
                    DatabaseException::EXCEPTION_ALREADY_EXISTS
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __METHOD__.'(): First argument must be an object descended from SchemaTable.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

    }

    public function addTrigger ($trigger, $name) {

        if (is_subclass_of($trigger, 'SchemaTrigger')) {
            if ($this->nameAvailable($name)) {
                $this->tables[$name] = $table;
            } else {
                throw new DatabaseException(
                    $this,
                    __METHOD__.'(): Name "'.$name.'" already exists in this schema.',
                    DatabaseException::EXCEPTION_ALREADY_EXISTS
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __METHOD__.'(): First argument must be an object descended from SchemaTrigger',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

    }

    public function addView ($view, $name) {

        if (is_subclass_of($view, 'SchemaView')) {
            if ($this->nameAvailable($name)) {

            } else {
                throw new DatabaseException(
                    $this,
                    __METHOD__.'(): Name "'.$name.'" already exists in this schema.',
                    DatabaseException::EXCEPTION_ALREADY_EXISTS
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __METHOD__.'(): First argument must be an object descended from SchemaTrigger',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

    }

    public function nameAvailable ($name) {

        return !$this->nameExists($name);

    }

    public function nameExists ($name) {

        if (
            isset($this->tables[$name])     ||
            isset($this->views[$name])      ||
            isset($this->triggers[$name])
        ) {
            return true;
        } else {
            return false;
        }

    }

}

?>
