<?php

class MySQLDatabase extends DatabaseModel implements DatabaseInterface {

    //TODO:
    //Actually implement the Grammar Table.

    protected $grammarTable = [

        'sql' => [

            'columnExists' => [
                'stmt' => 'SELECT * FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?;',
                'args' => [
                    [ 'value' => 'database',    'type' => PDO::PARAM_STR ],
                    [ 'value' => 'table',       'type' => PDO::PARAM_STR ],
                    [ 'value' => 'column',      'type' => PDO::PARAM_STR ]
                ]
            ],

            'getColumns' => [
                'stmt' => 'SELECT column_name FROM information_schema.column WHERE table_schema = ? AND table_name = ?;',
                'args' => [
                    [ 'value' => 'database',    'type' => PDO::PARAM_STR ],
                    [ 'value' => 'table',       'type' => PDO::PARAM_STR ]
                ]
            ],

            'getTables' => [
                'stmt' => 'SELECT * FROM information_schema.tables;',
                'args' => []
            ],

            'insert' => [
                'stmt' => 'INSERT INTO '.$this::PARAM_TABLE.' ( '.$this::PARAM_COLUMN_SET.' ) VALUES ( '.$this::PARAM_SET.' );',
                'tables' => [ 'table' ],
                'columns' => [ 'columns' ],
                'lists' => [ 'values' ]
            ],

            //TODO:
            //select is going to be difficult, because as it is now, the very
            //structure of the statement changes based on things such as whether
            //or not there are any conditions. This will most likely require
            //either splitting the query into selectBasic and selectWithCond
            //so that we can reflect these different structures, or possibly use
            //the callback field to provide a custom function.

            'tableExists' => [
                'stmt' => 'SHOW TABLES LIKE '.$this::PARAM_TABLE.';',
                'tables' => [ 'table' ]
            ]

        ]

    ];

}

?>
