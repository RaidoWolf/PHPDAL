<?php

interface DatabaseConditionInterface {

    public function __construct ($struct=[], &$backref, $table);
    public function __invoke (); //invocation should return statement.
    public function __toString (); //string conversion should return itself, serialized.
    public function add ($rule); //add rule to conditional statement
    public function del ($rule); //delete rule from conditional statement
    public function getStatement (); //returns the statement array ['stmt' => (string), 'args' => (array)]
    public function getStructure (); //returns the structure array (same format as input)
    public function parse (array $array, $encap=false); //parse array to statement in target language

}

?>
