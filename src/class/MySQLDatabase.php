<?php

class MySQLDatabase extends DatabaseModel implements DatabaseInterface {

    //TODO:
    //Actually implement the Grammar Table.

    protected $dbms = [

        /**
         * PDO DSN Configuration
         */
        'dsn' => [
            'prefix' => 'mysql',
            'args' => [
                [
                    'name' => 'host',
                    'value' => 'host',
                    'required' => true
                ],
                [
                    'name' => 'port',
                    'value' => 'port',
                    'required' => false
                ],
                [
                    'name' => 'dbname',
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
                'stmt' => 'SELECT * FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?;',
                'args' => [
                    [ 'value' => 'database',    'type' => self::TYPE_STR ],
                    [ 'value' => 'table',       'type' => self::TYPE_STR ],
                    [ 'value' => 'column',      'type' => self::TYPE_STR ]
                ]
            ],

            'getColumns' => [
                'stmt' => 'SELECT column_name FROM information_schema.column WHERE table_schema = ? AND table_name = ?;',
                'args' => [
                    [ 'value' => 'database',    'type' => self::TYPE_STR ],
                    [ 'value' => 'table',       'type' => self::TYPE_STR ]
                ]
            ],

            'getTables' => [
                'stmt' => 'SELECT * FROM information_schema.tables;',
                'args' => []
            ],

            'insert' => [
                'stmt' => 'INSERT INTO ?{table} ( ?{setcolumns} ) VALUES ( ${set} );',
                'args' => [
                    [ 'value' => 'columns',     'type' => self::TYPE_STR ],
                ],
                'tables' => [ 'table' ],
                'sets' => [ 'values' ],
                'columnSets' => [ 'columns' ]
            ],

            //TODO:
            //select is going to be difficult, because as it is now, the very
            //structure of the statement changes based on things such as whether
            //or not there are any conditions. This will most likely require
            //either splitting the query into selectBasic and selectWithCond
            //so that we can reflect these different structures, or possibly use
            //the callback field to provide a custom function.

            'tableExists' => [
                'stmt' => 'SHOW TABLES LIKE ?{table};',
                'tables' => [ 'table' ]
            ]

        ]

    ];

}

?>
