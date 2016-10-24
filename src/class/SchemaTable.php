<?php

class SchemaTable extends SchemaComponentModel implements SchemaComponentInterface {

    private $columns = [];

    public function __construct () {

        if (func_num_args() > 0) {
            if (func_num_args() == 1 && is_array(func_get_arg(0))) {
                foreach (func_get_arg(0) as $columnKey => $column) {
                    if (is_subclass_of($column, 'SchemaTableColumn')) {
                        $this->columns[] = $column;
                    } else {
                        throw new DatabaseException(
                            __METHOD__.'(): Column at ['.$columnKey.'] was not an instance of class SchemaTableColumn.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                            $this
                        );
                    }
                }
            } else {
                foreach (func_get_args() as $columnKey => $column) {
                    if (is_subclass_of($column, 'SchemaTableColumn')) {
                        $this->columns[] = $column;
                    } else {
                        throw new DatabaseException(
                            __METHOD__.'(): Column at argument position ['.$columnKey.'] was not an instance of class SchemaTableColumn.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE,
                            $this
                        );
                    }
                }
            }
        } else {
            throw new DatabaseException(
                __METHOD__.'(): Require minimum of one argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT,
                $this
            );
        }

    }

    public function getColumns () {

        return $this->columns;

    }

}

?>
