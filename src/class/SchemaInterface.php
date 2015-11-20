<?php

interface SchemaInterface {

    //TODO: Create Schema Interface.

    public function __construct ();
    public function __invoke ();
    public function __toString ();
    public function columnConform ($table, $column);
    public function columnMatches ($table, $column);
    public function getAllSchemas ();
    public function getCreateQuery ();
    public function getSchema ($name);
    public function handleValueIn ($table, $key, $value);
    public function handleValueOut ($table, $key, $value);
    public function schemaConform ();
    public function schemaMatches ();
    public function tableConform ($table);
    public function tableMatches ($table);

}

?>
