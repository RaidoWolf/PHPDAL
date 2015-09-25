<?php

class SQLiteDatabase implements DatabaseInterface {

    //TODO:
    //Actually implement the Grammar Table.

    protected $dbms = [

        /**
         * PDO DSN Configuration
         */
        'dsn' => [
            'prefix' => 'sqlite',
            'args' => [
                [
                    'name' => null,
                    'value' => 'name',
                    'required' => true
                ]
            ]
        ],

        /**
         * SQL Statement Configuration
         */
        'sql' => [

            'columnExists' => [
                'stmt' => 'PRAGMA table_info( ? );',
                'args' => [
                    [ 'value' => 'table',   'type' => self::TYPE_STR ]
                ],
                'action' => [
                    'id' => self::ACTION_IN_ARRAY,
                    'args' => [
                        [ 'name' => 'needle',   'value' => 'column' ],
                        [ 'name' => 'haystack', 'value' => 'results' ]
                    ]
                ]
            ],

            'getColumns' => [
                'stmt' => 'PRAGMA table_info( ? );',
                'args' => [
                    [ 'value' => 'table',   'type' => self::TYPE_STR ]
                ]
            ],

            'getTables' => [
                'stmt' => 'SELECT name FROM sqlite_master WHERE type = \'table\';',
                'args' => []
            ],

            'insert' => [
                'stmt' => 'INSERT INTO ?{table} ( ?{setcolumns} ) VALUES ( ?{set} );',
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
                    [ 'value' => 'table',   'type' => self::TYPE_STR ]
                ]
            ],

        ]

    ];

}

?>
