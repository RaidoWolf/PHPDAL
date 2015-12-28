<?php

class MySQLDatabase extends DatabaseModel implements CustomDatabaseInterface {

    //TODO:
    //Actually implement the Grammar Table.

    protected $dbms = [

        /**
         * DBMS-Specific Classes
         */
        'classes' => [
            'condition' => 'MySQLCondition'
        ],

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
                'stmt' => 'SELECT column_name FROM information_schema.columns WHERE table_schema = ? AND table_name = ?;',
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

            'selectConditional' => [
                'stmt' => 'SELECT ?{setcolumns} FROM ?{table} WHERE ${conditions} LIMIT ?, ?;',
                'args' => [
                    [ 'value' => 'start',   'type' => self::TYPE_INT ],
                    [ 'value' => 'limit',   'type' => self::TYPE_INT ]
                ],
                'tables' => [ 'table' ],
                'columnSets' => [ 'columns' ],
                'conditions' => [ 'conditions' ]
            ],

            'selectUnconditional' => [
                'stmt' => 'SELECT ?{setcolumns} FROM ?{table} LIMIT ?, ?;',
                'args' => [
                    [ 'value' => 'start',   'type' => self::TYPE_INT ],
                    [ 'value' => 'limit',   'type' => self::TYPE_INT ]
                ],
                'tables' => [ 'table' ],
                'columnSets' => [ 'columns' ]
            ],

            'tableExists' => [
                'stmt' => 'SHOW TABLES LIKE ?{table};',
                'tables' => [ 'table' ]
            ]

        ]

    ];

}

?>
