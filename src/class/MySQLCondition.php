<?php

final class MySQLConditionNew extends DatabaseConditionModel implements DatabaseConditionInterface {

    //TODO

}

final class MySQLCondition extends DatabaseConditionModelOld implements DatabaseConditionInterfaceOld {

    private $dbmsGrammarTable = [
        'quoteIdentLeft'    => '`', //string to be placed at the left of an identifier
        'quoteIdentRight'   => '`' //string to be placed at the right of an identifier
    ];

}

?>
