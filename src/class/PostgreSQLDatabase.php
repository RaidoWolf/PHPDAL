<?php

class PostgreSQLDatabase extends DatabaseModel implements CustomDatabaseInterface {

    const UPPER_LIMIT = 18446744073709551615;

    protected $dbms = [

        /**
         * DBMS-specific Classes
         */
        'classes' => [
            'condition' => 'PostgreSQLCondition'
        ],

        /**
         * PDO DSN Configuration
         */
        'dsn' => [
            'prefix' => 'pgsql',
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

            'delete' => [
                'stmt' => 'DELETE FROM ${table} WHERE ${condition} LIMIT ?, ?;',
                'args' => [
                    [ 'value' => 'start',       'type' => self::TYPE_INT ],
                    [ 'value' => 'limit',       'type' => self::TYPE_INT ]
                ],
                'tables' => [ 'table' ]
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
                'stmt' => 'INSERT INTO ?{table} ( ?{setcolumns} ) VALUES ( ?{set} );',
                'tables' => [ 'table' ],
                'columns' => [ 'columns' ],
                'lists' => [ 'values' ]
            ],

            'selectConditional' => [
                'stmt' => 'SELECT ?{setcolumns} FROM ?{table} WHERE ?{conditions} LIMIT ?, ?;',
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
                'stmt' => 'SELECT EXISTS ( SELECT 1 FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname = ? AND c.relname = ? AND c.relkind = \'r\');',
                'args' => [
                    [ 'value' => 'database',    'type' => self::TYPE_STR ],
                    [ 'value' => 'table',       'type' => self::TYPE_STR ]
                ]
            ]

        ]

    ];

}

?>
