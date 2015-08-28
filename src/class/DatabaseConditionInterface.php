<?php

interface DatabaseConditionInterface {

    public function __construct ($struct=[]);
    public function __invoke ();
    public function __toString ();
    public function arrayParse (array $array, $encap=false);
    public function getStatement ();
    public function getStructure ();
    public function insert ($array);
    public function remove ($array);

}

?>
