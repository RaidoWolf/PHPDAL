<?php

interface DatabaseExceptionInterface {

    public function __construct (&$caller, $message=null, $code=null, &$caught=null);
    public function &getCaller ();
    public function getConstants ();
    public function getErrorFlag ();

}

?>
