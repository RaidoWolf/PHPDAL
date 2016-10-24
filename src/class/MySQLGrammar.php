<?php

final class MySQLGrammar extends DatabaseGrammarModel implements DatabaseGrammarInterface {

    private $tableExtended = [
        'quoteIdentLeft' => '`', //string to be placed at the left of an identifier
        'quoteIdentRight' => '`', //string to be placed at the right of an identifier
    ];

}

?>
