<?php

class SQLiteDatabase implements DatabaseInterface {

    //TODO:
    //Actually implement the Grammar Table.

    protected $grammarTable = [

        'sql' => [

            'columnExists' => [
                'stmt' => 'PRAGMA table_info( ? );',
                'args' => [
                    [ 'value' => 'table',   'type' => PDO::PARAM_STR ]
                ],
                'callback' => function ($data, $args) {
                    if (in_array($args[0], $data)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            ],

            'getColumns' => [
                'stmt' => 'PRAGMA table_info( ? );',
                'args' => [
                    [ 'value' => 'table',   'type' => PDO::PARAM_STR ]
                ]
            ],

            'getTables' => [
                'stmt' => 'SELECT name FROM sqlite_master WHERE type = \'table\';'
                'args' => []
            ],

            'insert' => [
                'stmt' => 'INSERT INTO '.$this::PARAM_TABLE.' ( '.$this::PARAM_COLUMN_SET.' ) VALUES ('.$this::PARAM_SET.' );',
                'tables' => [ 'tables' ],
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
                'stmt' => 'SELECT name FROM sqlite_master WHERE type=\'table\' AND name = ?;',
                'table' => [
                    [ 'value' => 'table',   'type' => PDO::PARAM_STR ]
                ]
            ],

        ]

    ];

}

?>
