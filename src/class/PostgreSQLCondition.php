<?php

class PostgreSQLConditionNew extends DatabaseConditionModel implements DatabaseConditionInterface {

    //TODO

}

class PostgreSQLCondition extends DatabaseConditionModelOld implements DatabaseConditionInterfaceOld {

    protected $dbmsGrammarTable = [
        //PostgreSQL is standards-compliant, so this is not needed.
    ];

}

?>
