<?php

interface DatabaseConditionInterface {

    protected $dbmsGrammarTable; //array that maps query grammar to elements (see DatabaseConditionModel.php for an example)
    protected $standardGrammarTable;

    public function __construct ($struct=[]);
    public function __invoke ();
    public function __toString ();
    public function add ($rule); //add rule to conditional statement
    public function del ($rule); //delete rule from conditional statement
    protected function getGrammar();
    public function getStatement ();
    public function getStructure ();
    public function parse ($string); //parse standard SQL statement into array
    public function parseArray (array $array, $encap=false); //parse array to statement in target language

}

?>
