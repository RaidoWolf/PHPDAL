<?php

final class PostgreSQLConditionNew extends DatabaseConditionModel implements DatabaseConditionInterface {

    //TODO

}

final class PostgreSQLCondition extends DatabaseConditionModelOld implements DatabaseConditionInterfaceOld {

    private $dbmsGrammarTable = [
        //PostgreSQL is standards-compliant, so this is not needed.
    ];

}

?>
