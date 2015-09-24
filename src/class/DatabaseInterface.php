<?php

interface DatabaseInterface {

    public function __construct (
        $name//,
        //$user,
        //$pass,
        //$host,
        //$port,
        //$table
    ); //only DBMS servers require last few variables, but implement them in order please
    public function __invoke ();
    public function __toString ();
    public function columnExists (); //check if column exists in table
    public function createTable (); //create new table in database
    public function createTrigger (); //create new trigger in database
    public function createView (); //create new view in database
    public function delete (); //delete rows from table
    public function drop (); //drop table/trigger/view
    public function genStmt (
        $stmt,
        $tables = null,
        $columns = null,
        $sets = null,
        $tableSets = null,
        $columnSets = null
    ); //generate preparable statement with dynamic identifiers
    public function getColumns ($table = null); //get an array of columns in table
    public function getDefaultTable (); //get the default table in object
    public function getTables (); //get an array of tables in database
    public function hasDefaultTable (); //check if object has a default table
    public function insert ($in, $table=null); //insert rows into table
    public function select (
        $columns=['*'],
        $conditions=null,
        $start=null,
        $count=null,
        $sortBy=null,
        $sortDirection=null,
        $table=null
    ); //select rows from table
    public function tableExists ($table); //check if the table exists
    public function update ($array); //update rows in the table

    /**
     * Available Protected Methods
     */

     //protected function quoteColumn ($string); //quote column names
     //protected function quoteTable ($string); //quote table names

     /**
      * Available Member Variables
      */

      //protected $dbms //DBMS-specific configuration

}

?>
