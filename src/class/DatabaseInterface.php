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

    //protected $dbms //DBMS-specific configuration (use this to define DBMS grammar)

    public function __construct (
        $name//,
        //$user,
        //$pass,
        //$host,
        //$port,
        //$table
    ); //only DBMS servers require last few variables, but implement them in order please
    public function __invoke (); //return true if the object is useable, false if not
    public function __toString (); //serialize the object
    public function columnExists (); //check if column exists in table
    public function createTable (); //create new table in database
    public function createTrigger (); //create new trigger in database
    public function createView (); //create new view in database
    public function delete (); //delete rows from table
    public function drop (); //drop table/trigger/view
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

}

?>
