<?php

interface CrossDatabaseInterface extends DatabaseInterface {

    public function __construct ($type, $name, $user=null, $pass=null, $host='localhost', $port=null, $table=null);
    public function getChild ();
    public function getType ();

}

?>
