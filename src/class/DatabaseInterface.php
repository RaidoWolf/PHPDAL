<?php

interface DatabaseInterface {

    public function __construct ($name /*, $user, $pass, $host, $port, $table */); //only DBMS servers require last few variables
    public function __invoke ();
    public function __toString ();
    public function columnExists (); //check if column exists in table
    public function createTable (); //create new table in database
    public function createTrigger (); //create new trigger in database
    public function createView (); //create new view in database
    public function escapeColumn (); //escape column names
    public function escapeLiteral (); //escape literal data
    public function escapeTable (); //escape table names
    public function delete (); //delete rows from table
    public function drop (); //drop table/trigger/view
    public function genStmt ($stmt, $tables, $columns); //generate preparable statement with dynamic identifiers
    public function getColumns (); //get an array of columns in table
    public function getDefaultTable (); //get the default table in object
    public function getTables (); //get an array of tables in database
    public function hasDefaultTable (); //check if object has a default table
    public function insert ($in, $table=null); //insert rows into table
    public function select ($columns=['*'], $conditions=null, $start=null, $count=null, $sortBy=null, $sortDirection=null, $table=null); //select rows from table
    public function tableExists ($table); //check if the table exists
    public function update ($array); //update rows in the table

}

?>
