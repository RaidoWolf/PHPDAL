<?php

class SchemaTrigger extends SchemaComponentModel implements SchemaComponentInterface {

    const TIMING_NONE = 0;
    const TIMING_BEFORE = 1;
    const TIMING_AFTER = 2;

    const EVENT_NONE = 0;
    const EVENT_INSERT = 1;
    const EVENT_UPDATE = 2;

    private $timing = self::TIMING_NONE;
    private $event = self::EVENT_NONE;
    private $table = '';
    private $code = '';

    public function __construct ($timing, $event, $table, $code) {

        if (1 <= $timing && $timing <= 2) {
            $this->timing = $timing;
        } else {
            throw new DatabaseException(
                __METHOD__.'(): Invalid argument given for timing. See '.__CLASS__.'::TIMING_* constants.',
                DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                $this
            );
        }

        if (1 <= $timing && $timing <= 2) {
            $this->event = $event;
        } else {
            throw new DatabaseException(
                __METHOD__.'(): Invalid argument given for event. See '.__CLASS__.'::EVENT_* constants.',
                DatabaseException::EXCEPTION_INPUT_NOT_VALID,
                $this
            );
        }

        if (is_string($table)) {
            $this->table = $table;
        } else {
            throw new DatabaseException(
                __METHOD__.'(): Table argument must be a string.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                $this
            );
        }

        if (is_string($code)) {
            $this->code = $code;
        } else {
            throw new DatabaseException(
                __METHOD__.'(): Code argument must be a string.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                $this
            );
        }

    }

    public function getCode () {

        return $this->code;

    }

    public function getEvent () {

        return $this->event;

    }

    public function getTable () {

        return $this->table;

    }

    public function getTiming () {

        return $this->timing;

    }

}

?>
