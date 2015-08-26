<?php

interface CrossDatabaseInterface extends DatabaseInterface {

    public function __construct ($type, $name, $user=null, $pass=null, $host=null, $port=null, $table=null);

}

?>
