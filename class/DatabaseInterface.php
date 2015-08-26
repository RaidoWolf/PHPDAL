<?php

interface DatabaseInterface {

    public function __construct ($name, $user=null, $pass=null, $host=null, $port=null, $table=null);
    public function __invoke ();
    public function __toString ();
    public function columnExists ();
    public function getColumns ();
    public function getDefaultTable ();
    public function getTables ();
    public function getType ();
    public function hasDefaultTable ();
    public function insert ($in, $table=null);
    public function select ($columns=['*'], $conditions=null, $start=null, $count=null, $sortBy=null, $sortDirection=null, $table=null);
    public function tableExists ($table);
    

}

?>
