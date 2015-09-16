<?php

interface DatabaseConditionInterface {

    //Protected Members
    protected $dbmsGrammarTable; //array that maps query grammar to elements (see DatabaseConditionModel.php for an example)
    protected $standardGrammarTable;

    //Public Methods
    public function __construct ($struct=[]);
    public function __invoke (); //invocation should return statement.
    public function __toString (); //string conversion should return itself, serialized.
    public function add ($rule); //add rule to conditional statement
    public function del ($rule); //delete rule from conditional statement
    public function getStatement (); //returns the statement array ['stmt' => (string), 'args' => (array)]
    public function getStructure (); //returns the structure array (same format as input)
    public function parse (array $array, $encap=false); //parse array to statement in target language

    //Protected Methods
    protected function getGrammar(); //gets values from the two grammar tables, preferring DBMS and falling back to standard

}

?>
