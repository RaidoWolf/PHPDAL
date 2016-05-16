<?php

interface SchemaInterface {

    public function __construct ();
    public function addTable ($table, $name);
    public function addTrigger ($trigger, $name);
    public function addView ($view, $name);
    public function nameAvailable ($name);
    public function nameExists ($name);

}

?>
