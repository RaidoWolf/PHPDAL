<?php

/**
 * DatabaseInterface Interface
 * ===========================
 *
 * Defines the standard protocol/interface for interacting with standardized
 * database objects. This is implemented by DatabaseModel and, must also be
 * implemented by all DBMS-specific extensions of DatabaseModel.
 * NOTE: developers, please read comments to ensure you are actually correctly
 * implementing this interface.
 *
 * @package PHPDAL
 * @author Alexander Barber
 */
interface DatabaseInterface {

    //private $dbms //DBMS-specific configuration (use this to define DBMS grammar)

    public function __invoke (); //return true if the object is useable, false if not
    public function __toString (); //serialize the object
    public function columnExists ($column, $table = null); //check if column exists in table
    //public function createTable (); //create new table in database
    //public function createTrigger (); //create new trigger in database
    //public function createView (); //create new view in database
    //public function delete (); //delete rows from table
    //public function drop (); //drop table/trigger/view
    //public function exec ( //prepare and execute a statement
    //    $query,
    //    $args           = [],
    //    $tables         = [],
    //    $columns        = [],
    //    $sets           = [],
    //    $tablesets      = [],
    //    $columnsets     = [],
    //    $conditions     = [],
    //    $action         = self::ACTION_NONE,
    //    $actionargs     = []
    //);
    public function delete ( //delete rows matching condition
        $condition,
        $start          = null,
        $limit          = null,
        $table          = null
    );
    public function getColumns ( //get an array of columns in table
        $table          = null
    );
    public function getDefaultTable (); //get the default table in object
    public function getTables (); //get an array of tables in database
    public function hasDefaultTable (); //check if object has a default table
    public function insert ( //insert rows into table
        $in,
        $table          = null
    );
    public function select (
        $columns        = ['*'],
        $conditions     = null,
        $start          = null,
        $count          = null,
        $sortBy         = null,
        $sortDirection  = null,
        $table          = null
    ); //select rows from table
    public function tableExists ( //check if the table exists
        $table
    );
    //public function update ( //update rows in the table
    //    $array
    //);

}

?>
