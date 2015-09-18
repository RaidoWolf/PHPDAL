<?php

<?php

class MySQLDatabase implements DatabaseInterface {

    // -- PROPERTIES/MEMBERS -- //

    protected $config = [
        'defaultPort' => 3306,
        'defaultEncoding' => 'utf8'
    ];
    protected $connector;
    protected $encoding;
    protected $host;
    protected $info;
    protected $lastError;
    protected $lastStackTrace;
    protected $name;
    protected $open;
    protected $port;
    protected $table;

    // -- CONSTANTS/FLAGS -- //

    const FIELD_DATA = 0;
    const FIELD_TABLE = 1;
    const FIELD_COLUMN = 2;

    const KEYWORD_NONE = 0;
    const KEYWORD_ALL = 1;

    const SORT_NONE = 0;
    const SORT_ASC = 1;
    const SORT_DESC = 2;

    /**
     * Constructor Method
     * @param int $type (required) -
     * @param string $name (required) -
     * @param string $user (required for secured) -
     * @param string $pass (required for secured) -
     * @param string $host (required for remote, optional because it defaults to localhost) -
     * @param integer $port (required for remote, optional because it defaults to server type's default port) -
     * @param strign $table (optional) -
     */
    public function __construct (
        $name,
        $user   = null,
        $pass   = null,
        $host   = 'localhost',
        $port   = null,
        $table  = null
    ) {

        /*
         * Input Handling for $name (database name)
         *
         * $name is required, must be a string, and must be a valid database name,
         * but we cannot check if it's a valid name until we connect.
         */
        if (isset($name)) {
            //database name is set
            if (gettype($name) != 'string') {
                //database name is not a string
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered database name argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            //database name is not set
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): missing required database name argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
        }

        /*
         * Input Handling for $user (username)
         *
         * $user is only required for servers that require authentication. We cannot
         * test if authentication is required until after connecting. $user also must
         * be a string.
         */
        if ($user !== null) {
            //username is set
            if (gettype($user) != 'string') {
                //username is not a string
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered username argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        }

        /*
         * Input Handling for $pass (password)
         *
         * $pass is only required for servers that require authentication. We cannot
         * test if authentication is required until after connecting. $pass also must
         * be a string.
         */
        if ($pass !== null) {
            //password is set
            if (gettype($pass) != 'string') {
                //password is not a string
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered password argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        }

        /*
         * Input Handling for $host (server hostname)
         *
         * $host is only required for remote database servers (so not sqlite). However,
         * $host already has a default value of 'localhost', which means that it doesn't
         * have to be provided in an argument if using localhost. $host must be a string,
         * $host must be a valid IP address, hostname, or domain name, and if there is a
         * port after a : in the URL, that must be separated into $port.
         */
        if ($host != 'localhost') {
            //hostname is set
            if (gettype($host) == 'string') {
                //hostname is a string
                if (!DatabaseTools::isValidHost($host)) {
                    //hostname has invalid syntax
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): encountered invalid given hostname (do not include URI scheme, port numbers, or paths!).',
                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                    );
                }
            } else {
                //hostname is not a string
                throw new DatabaseException(
                    $this,
                    __CLASS_.'->'.__METHOD__.'(): encountered hostname argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        }

        /*
         * Input Handling for $port (server port number)
         *
         * $port is only required for remote database servers (so not sqlite). However,
         * $port will be given a default value based on the server type. $port must be an
         * integer greater than or equal to 0 and less than or equal to 65535. The actual
         * validity of the port cannot be validated until trying to connect to the server.
         */
        if ($port !== null) {
            //port is set
            if (!is_int($port)) {
                //port is not an integer
                if (is_numeric($port)) {
                    $port = (integer)$port;
                } else {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): encountered port number argument of invalid type.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
            }
            if (0 > $port || $port > 65535) {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered port number argument outside of legal bounds.',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
            }
        } else {
            //port is not set
            if ($this->type == self::TYPE_MYSQL) {
                $port = 3306;
            } elseif ($this->type == self::TYPE_PGSQL) {
                $port = 5432;
            }
        }

        /*
         * Input Handling for $table (default table)
         *
         * $table is optional. This only has to be set when a default table is required.
         * If it is not set, other methods will need to be given a table argument or they will
         * error. Table must be a string, and must be a valid name for a SQL table. We won't know
         * if the default table is valid until after the database connection is established, but
         * it will be tested then.
         */
        if ($table !== null) {
            //table is set
            if (gettype($table) != 'string') {
                //table is not a string
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered table argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        }

        //Set class properties/members
        $this->type     = $type;
        $this->name     = $name;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->host     = $host;
        $this->port     = $port;
        $this->table    = $table;

        //generate DSN
        if ($type == self::TYPE_MYSQL) {
            $dsn = 'mysql:';
            $dsn .= "host=$host;";
            $dsn .= "port=$port;";
            $dsn .= "dbname=$name";
        } elseif ($type == self::TYPE_PGSQL) {
            $dsn = 'pgsql:';
            $dsn .= "host=$host;";
            $dsn .= "port=$port;";
            $dsn .= "dbname=$name";
        } elseif ($type == self::TYPE_SQLITE) {
            $dsn = 'sqlite:';
            $dsn .= "$name";
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): invalid database type given.',
                DatabaseException::EXCEPTION_INPUT_NOT_VALID
            );
        }

        //create PDO object with DSN (this is the actual connection)
        try {
            $this->connector = new PDO($dsn, $user, $pass);
            $this->connector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connector->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): caught exception when opening the database connection',
                DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                $e
            );
        }

    }

    /**
     * Destructor Method
     */
    public function __destruct () {

        //dunno, PHP takes care of destruction pretty well

    }

    /**
     * Invocation Method
     */
    public function __invoke () {

        return $this->open;

    }

    /**
     * String Conversion Method
     */
    public function __toString () {

        return serialize($this);

    }

    public function columnExists ($column, $table = null) {

        if ($table == null) {
            if ($this->hasDefaultTable()) {
                $table = $this->getDefaultTable();
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): missing required table definition or argument.',
                    DatabaseException::EXCEPTION_MISSING_DEFINITION
                );
            }
        } else {
            if (!$this->tableExists($table)) {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): table provided in argument does not exist.',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
                //just in case someone decides to be all dangerous and catch that exception.
                return false; //obviously it doesn't exist, neither does the table.
            }
        }

        if ($this->type == self::TYPE_MYSQL) {
            $query = '
                SELECT *
                    FROM information_schema.columns
                    WHERE
                        TABLE_SCHEMA = ? AND
                        TABLE_NAME = ? AND
                        COLUMN_NAME = ?;
            ';
            $stmt = $this->connector->prepare($query);
            $stmt->bindParam(1, $this->name, PDO::PARAM_STR);
            $stmt->bindParam(2, $table, PDO::PARAM_STR);
            $stmt->bindParam(3, $column, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($results) > 0) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->type == self::TYPE_PGSQL) {
            $query = '
                SELECT *
                    FROM information_schema.columns
                    WHERE
                        TABLE_SCHEMA = ? AND
                        TABLE_NAME = ? AND
                        COLUMN_NAME = ?;
            ';
            $stmt = $this->connector->prepare($query);
            $stmt->bindParam(1, $this->name, PDO::PARAM_STR);
            $stmt->bindParam(2, $table, PDO::PARAM_STR);
            $stmt->bindParam(3, $column, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($results) > 0) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->type == self::TYPE_SQLITE) {
            $query = 'PRAGMA table_info( ? );';
            $stmt = $this->connector->prepare($query);
            $stmt->bindParam(1, $table, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (in_array($column, $results)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): invalid database type in object.',
                DatabaseException::EXCEPTION_CORRUPTED_OBJECT
            );
        }

    }

    public function genStmt ($stmt, $tables = null, $columns = null, $table = null) {

        if (isset($stmt)) {

            //optional tables array missing
            if ($tables == null) {
                $tables = []; //default empty array
            }
            //optional columns array missing
            if ($columns == null) {
                $columns = []; //default empty array
            }

            //validate types
            if (is_string($stmt) && is_array($tables) && is_array($columns) && (is_string($table) || $table == null)) {

                if (count($tables) != 0) {
                    $validTables = $this->getTables();
                    foreach ($tables as $table) {
                        if (in_array($table, $validTables)) {

                        } else {
                            throw new DatabaseException(
                                $this,
                                __CLASS__.'->'.__METHOD__.'(): table "'.$table.'" does not exist.',
                                DatabaseException::EXCEPTION_INPUT_NOT_VALID
                            );
                            $stmt = DatabaseUtils::replaceOnce(DatabaseUtils::PARAM_TABLE, '', $stmt); //delete this placeholder (in case exception is caught)
                            array_shift($table); //shift out the table (in case exception is caught)
                        }
                    }
                    foreach ($columns as $column) {

                        $typeof_column = gettype($column);

                        $columnsTable = [];
                        if (isset($this->table)) {
                            if (!in_array($this->table, $validTables)) {
                                throw new DatabaseException(
                                    $this,
                                    __CLASS__.'->'.__METHOD__.'(): default table "'.$this->table.'" does not exist.',
                                    DatabaseException::EXCEPTION_CORRUPTED_OBJECT
                                );
                            }
                        }
                        $columnsTable[$this->table] = $this->getColumns($this->table);

                        if ($typeof_column == 'string') {
                            //TODO: check $table, then $this->table, then exception. do the replacement if any are found.
                        } elseif ($typeof_column == 'array') {
                            if (array_key_exists('table', $column) && array_key_exists('column', $column)) {
                                if (!array_key_exists($column['table'], $columnsTable)) {
                                    $columnsTable[$column['table']] = $this->getColumns($column['table']);
                                }
                                if (in_array($column['column'], $columnTable[$column['table']])) {
                                    $stmt = DatabaseUtils::replaceOnce(DatabaseUtils::PARAM_COLUMN, $this->quoteColumn($column['column']), $stmt);
                                } else {
                                    throw new DatabaseException(
                                        $this,
                                        __CLASS__.'->'.__METHOD__.'(): column "'.$column['column'].'" does not exist in table "'.$column['table'].'".',
                                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                    );
                                }
                            } else {
                                throw new DatabaseException(
                                    $this,
                                    __CLASS__.'->'.__METHOD__.'(): encountered invalid [\'table\',\'column\'] array structure.',
                                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                                );
                                $stmt = DatabaseUtils::replaceOnce(DatabaseUtils::PARAM_COLUMN, '', $stmt); //delete this placeholder (in case exception is caught)
                                array_shift($table); //shift out the table (in case exception is caught)
                            }
                        } else {
                            throw new DatabaseException(
                                $this,
                                __CLASS__.'->'.__METHOD__.'(): encountered column parameter of invalid type.',
                                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                            );
                        }
                    }
                }

            } else {
                //$stmt type violation
                if (!is_string($stmt)) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): encountered statement of non-string type.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
                //$tables type violation
                if (!is_array($tables)) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): encountered table array of non-array type.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
                //$columns type violation
                if (!is_array($columns)) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): encountered column array of non-array type.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
                //$table type violation
                if (!is_string($table) && $table != null) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): encountered table argument of invalid type.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): missing required statement string argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
        }

    }

    /**
     * DatabaseModel->getColumns() Method
     *
     * @param string $table (optional) - name of table, unless table defined by constructor
     * @return array - array of columns in the table
     */
    public function getColumns ($table = null) {

        if ($table == null) {
            if ($this->table != null) {
                $table = $this->table;
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): missing table setting and/or table argument.',
                    DatabaseException::EXCEPTION_MISSING_DEFINITION
                );
            }
        }

        if ($this->type == self::TYPE_MYSQL) {
            $statement = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`=\'?\' AND `TABLE_NAME`=\'?\';';
            $parameters = [$this->name, $table];
        } elseif ($this->type == self::TYPE_PGSQL) {
            $statement = 'SELECT column_name FROM information_schema.columns WHERE table_schema=\'?\' AND table_name=\'?\';';
            $parameters = [$this->name, $table];
        } elseif ($this->type == self::TYPE_SQLITE) {
            $statement = 'PRAGMA table_info(\'?\');';
            $parameters = [$table];
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): invalid database type setting.',
                DatabaseException::EXCEPTION_INPUT_NOT_VALID
            );
        }

        //prepare and execute the statement
        $stmt = $this->connector->prepare($statement);
        try {
            $exec = $stmt->execute([$parameters]);
        } catch (PDOException $e) {
            throw new DatabaseException (
                $this,
                __CLASS__.'->'.__METHOD__.'(): caught exception thrown by PDO.',
                DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                $e
            );
        }

        //fetch and return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getDefaultTable () {

        return $this->table;

    }

    public function getTables () {

        if ($this->type == self::TYPE_MYSQL) {
            $query = 'SELECT * FROM information_schema.tables;';
            $stmt = $this->connector->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($this->type == self::TYPE_PGSQL) {
            $query = 'SELECT * FROM information_schema.tables;';
            $stmt = $this->connector->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($this->type == self::TYPE_SQLITE) {
            $query = 'SELECT name FROM sqlite_master WHERE type=\'table\';';
            $stmt = $this->connector->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): invalid database type in object.',
                DatabaseException::EXCEPTION_CORRUPTED_OBJECT
            );
        }

        return $results;

    }

    /**
     * DatabaseModel->getType() Getter Method
     * @return string $this->type
     */
    public function getType () {

        return $this->type;

    }

    public function hasDefaultTable () {

        if (isset($this->table) && $this->table != null) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * DatabaseModel->insert() Method
     *
     * @param array $in (required) - associative array of input to be inserted, keys being the name of columns
     * @param string $table (optional if defined in constructor) - table to use
     * @return Database - reference to self
     */
    public function insert ($in, $table = null) {

        $typeof_table = gettype($table);
        if ($table != null) {
            if ($typeof_table != 'string') {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered table argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            } else {
                if (!$this->tableExists($table)) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): table "'.$table.'" does not exist.',
                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                    );
                }
            }
        }
        if (!$this->tableExists($table)) {
            $typeof_in = gettype($in);
        }
        if ($typeof_in == 'array') {
            //input is an array

            //generate the statement
            if ($this->type == self::TYPE_MYSQL) {
                //MySQL version
                $qmArray = array();
                for ($i; $i < count($in); $i++) {
                    $qmArray[] = '?';
                }
                $query  = 'INSERT INTO ? (';
                $query .= implode(',', $qmArray);
                $query .= ') VALUES (';
                $query .= implode(',', $qmArray);
                $query .= ');';
            } elseif ($this->type == self::TYPE_PGSQL) {
                //PostgreSQL version
                $qmArray = array();
                for ($i; $i < count($in); $i++) {
                    $qmArray[] = '?';
                }
                $query  = 'INSERT INTO ? (';
                $query .= implode(',', $qmArray);
                $query .= ') VALUES (';
                $query .= implode(',', $qmArray);
                $query .= ');';
            } elseif ($this->type == self::TYPE_SQLITE) {
                //SQLite3 version
                $qmArray = array();
                for ($i; $i < count($in); $i++) {
                    $qmArray[] = '?';
                }
                $query  = 'INSERT INTO ? (';
                $query .= implode(',', $qmArray);
                $query .= ') VALUES (';
                $query .= implode(',', $qmArray);
                $query .= ');';
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): invalid database type in object.',
                    DatabaseException::EXCEPTION_CORRUPTED_OBJECT
                );
            }

            //prepare the statement
            $statement = $this->connector->prepare($query);

            //execute the statement
            $depth = DatabaseTools::arrayDepth($in);
            if ($depth == 1) {
                //array depth 1 - single row insert
                $executionArray = array($table);
                foreach ($in as $inKey => $inVal) {
                    $executionArray[] = (string)$inKey;
                }
                foreach ($in as $inKey => $inVal) {
                    $executionArray[] = (string)$inVal;
                }
                try {
                    $statement->execute($executionArray);
                } catch (PDOException $e) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): caught exception thrown by PDO.',
                        DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                        $e
                    );
                }
            } elseif ($depth == 2) {
                //array depth 2 - multi-row insert
                $status = array();
                foreach ($in as $inVal) {
                    $executionArray = array($table);
                    foreach ($inVal as $inValKey => $inValVal) {
                        $executionArray[] = (string)$inValKey;
                    }
                    foreach ($inVal as $inValKey => $inValVal) {
                        $executionArray[] = (string)$inValVal;
                    }
                    try {
                        $statement->execute($executionArray);
                    } catch (PDOException $e) {
                        throw new DatabaseException(
                            $this,
                            __CLASS__.'->'.__METHOD__.'(): caught exception thrown by PDO.',
                            DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                            $e
                        );
                    }
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered array of excessive depth. Max depth = 2.',
                    DatabaseException::EXCEPTION_INPUT_ARRAY_TOO_DEEP
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): encountered input of invalid type. Must be an array.',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

        return $this; //for method chaining (since this is a mutator)

    }

    /**
     * DatabaseModel->quoteColumn() Method
     *
     * Quote column identifiers for dynamically generated statements.
     * NOTE: THIS DOES NOT ESCAPE DATA! It is only meant as a precaution against
     * unusual characters causing errors in statement execution.
     * DEVELOPERS: Please make sure all data using this is also checked against
     * an array of known valid columns in the table. Never require the end-user
     * to do escaping themselves.
     *
     * @param   string              $string - The column name to quote
     * @return  string              The quoted version of the input string
     * @throws  DatabaseException   If you dun goof
     */
    protected function quoteColumn ($string) {

        if (isset($string)) {
            if (is_string($string)) {
                $string = str_replace('`', '``', $string);
                return '`'.$string.'`';
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): given argument was not of type string.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): missing required argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
        }

    }

    /**
     * DatabaseModel->quoteTable() Method
     *
     * Quote table identifiers for dynamically generated statements.
     * NOTE: THIS DOES NOT ESCAPE DATA! It is only meant as a precaution against
     * unusual characters causing errors in statement execution.
     * DEVELOPERS: Please make sure all data using this is also checked against
     * an array of known valid columns in the table. Never require the end-user
     * to do escaping themselves.
     *
     * @param   string              $string - the table name to quote
     * @return  string              The quoted version of the input string
     * @throws  DatabaseException   If you dun goof
     */
    protected function quoteTable ($string) {

        if (isset($string)) {
            if (is_string($string)) {
                $string = str_replace('`', '``', $string);
                return '`'.$string.'`';
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): given argument was not of type string.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): missing required argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
        }

    }

    /**
     * DatabaseModel->select() Method
     *
     * @param array|string $columns (optional) - columns to return. if left empty/null, will default to all columns (*)
     * @param DatabaseCondition|array|string $conditions (optional) - conditions to lookup. If left empty/null, will default to no conditions.
     * @param int $start (optional) - starting index from which to begin selecting. If left empty, defaults to 0.
     * @param int $count (optional) - maximum number of results to return. If left empty, defaults to no limit.
     * @param string $sortBy (optional) - column with which to sort the table. If left empty, defaults to the first column in the table.
     * @param int $sortDirection (optional - (uses flags) direction to sort the table. If left empty, defaults to none. Options are SORT_NONE, SORT_ASC, SORT_DESC
     * @param string $table (optional if table set in constructor) - table from which to select.
     * return array - results of the select query as an associative array.
     */
    public function select ($columns = ['*'], $conditions = null, $start = null, $count = null, $sortBy = null, $sortDirection = null, $table = null) {

        //INPUT HANDLING

        //$columns validation
        $typeof_columns = gettype($columns);
        if ($typeof_columns == 'array') {
            //array is the desired input type, but needs no processing at this time
        } elseif ($typeof_columns == 'string') {
            $columns = str_getcsv($columns);
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): encountered columns argument of invalid type',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

        //$conditions validation
        $typeof_conditions = gettype($conditions);
        if ($conditions != null) {
            if ($typeof_conditions == 'object') {
                $classof_conditions == get_class($conditions);
                if ($classof_conditions == 'DatabaseCondition') {

                } else {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): object not of class DatabaseCondtion provdided for conditions argument.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
            } elseif ($typeof_conditions == 'array') {
                $conditions = new DatabaseCondition($conditions);
            } elseif ($typeof_conditions == 'string') {
                if (!$conditions = json_decode($conditions, true)) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): string provided for conditions argument failed to be parsed as JSON.',
                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                    );
                }
                $conditions = new DatabaseCondition($conditions);
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered conditions argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            $conditions = new DatabaseCondition([]); //default to no conditions
        }

        //$start validation
        if ($start != null) {
            if (is_int($start)) {
                if ($start < 0) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): negative number given for start index argument.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered start index argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            $start = null;
        }

        //$count validation
        if ($count != null) {
            if (is_int($count)) {
                if ($count < 0) {
                    throw new DatabaseException(
                            $this,
                            __CLASS__.'->'.__METHOD__.'(): negative number given for count argument.',
                            DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                    );
                }
            } else {
                throw new DatabaseException(
                        $this,
                        __CLASS__.'->'__METHOD__.'(): encountered count argument of invalid type.',
                        DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            $count = null;
        }

        //$start validation
        if ($start != null && $count == null) {
            //only start index is set
            $count = '18446744073709551615'; //good luck storing a database with that many rows (MySQL upper limit).
        } elseif ($start == null && $count != null) {
            //only row count is set
            $start = '0';
        }

        //$sortDirection validation
        if ($sortDirection != null) {
            if (
                $sortDirection != self::SORT_NONE &&
                $sortDirection != self::SORT_ASC &&
                $sortDirection != self::SORT_DESC
            ) {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): invalid sort direction provided.',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
            }
        } else {
            $sortDirection == self::SORT_NONE;
        }

        //$table validation
        $typeof_table = gettype($table);
        if ($table != null) {
            //table argument given
            if ($typeof_table == 'string') {
                //table argument is a string
                if (!$this->tableExists($table)) {
                    //table given in argument doesn't exist
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): table "'.$table.'" does not exist.',
                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                    );
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered table argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            //table argument not given
            if ($this->hasDefaultTable()) {
                $table = $this->getDefaultTable();
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): missing default table and/or table argument.',
                    DatabaseException::EXCEPTION_MISSING_DEFINITION
                );
            }
        }

        //$sortBy validation
        //this section is here and not ordered correctly because it needs to know validated $table
        if ($sortBy != null) {
            if (is_string($sortBy)) {
                if (!in_array($sortBy, $this->getColumns($table))) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): sort column "'.$sortBy.'" does not exist.',
                        DatabaseException::EXCEPTION_INPUT_NOT_VALID
                    );
                }
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): encountered sort-by argument of invalid type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            $sortBy == $this->getColumns($table)[0];
        }

        //COLUMN KEYWORD DETECTION
        if (in_array('*', $columns) || in_array('ALL', $columns)) {
            $columnKeyword = self::KEYWORD_ALL;
        } else {
            //CUSTOM SANITIZATION
            //(for stuff that can't be parameterized in a prepared statement)
            $columnCount = count($columns);
            for ($i = 0; $i < $columnCount; $i++) {
                if (!$this->columnExists($columns[$i])) {
                    throw new DatabaseException(
                        $this,
                        __CLASS__.'->'.__METHOD__.'(): column "'.$columns[$i].'" does not exist.',
                        DatabaseException::EXCEPTION_DB_ITEM_DOES_NOT_EXIST
                    );
                    unset($columns[$i]);
                }
            }
            $columns = array_values($columns); //normalize the columns (to fix gaps)

            //add columns to execution arrays
            $executionTypeArray = [];
            for ($i = 0; $i < count($columns); $i++) {
                $executionTypeArray[] = PDO::PARAM_STR;
            }
            $executionArray = $columns;
        }

        //DATABASE EXECUTION
        //build statement
        $query  = 'SELECT ';
        if (!$columnKeyword) {
            $query .= implode(', ', $columns); //this is being sanitized by checking that all values actually exist
        } else {
            if ($columnKeyword == self::KEYWORD_ALL) {
                $query .= '*';
            } else {
                throw new DatabaseException(
                    $this,
                    __CLASS__.'->'.__METHOD__.'(): invalid keyword caught (check columns argument).',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
            }
        }
        $query .= ' FROM '.$table;
        $query .= (strlen($conditions->getStatement()['stmt'] > 0) ? ' WHERE ' : '');
        $query .= (string)$conditions->getStatement()['stmt'];
        $query .= $start != null && $count != null ? ' LIMIT ?, ?' : '';
        $query .= ';';

        //add limit parameters to execution arrays
        if ($start != null && $count != null) {
            $executionArray[] = $start;
            $executionTypeArray[] = PDO::PARAM_INT;
            $executionArray[] = $count;
            $executionTypeArray[] = PDO::PARAM_INT;
        }
        $executionArray = array_merge($executionArray, $conditions->getStatement()['args']);

        try {
            //prepare statement and execute
            $stmt = $this->connector->prepare($query);
            for ($i = 0; $i < count($executionArray); $i++) {
                $stmt->bindValue($i + 1, $executionArray[$i], $executionTypeArray[$i]);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): caught exception thrown by PDO.',
                DatabaseException::EXCEPTION_GENERIC_DATABASE_ERROR,
                $e
            );
        }

        //return result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function tableExists ($table) {

        if ($this->type == self::TYPE_MYSQL) {
            $query = 'SHOW TABLES LIKE ?;';
            $stmt = $this->connector->prepare($query);
            $stmt->bindParam(1, $table, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($results > 0)) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->type == self::TYPE_PGSQL) {
            $query = '
                SELECT EXISTS (
                    SELECT  1
                    FROM    pg_catalog.pg_class c
                    JOIN    pg_catalog.pg_namespace n ON n.oid = c.relnamespace
                    WHERE   n.nspname = ?
                    AND     c.relname = ?
                    AND     c.relkind = \'r\'
                );
            ';
            $stmt = $this->connector->prepare($query);
            $stmt->bindParam(1, $this->name, PDO::PARAM_STR);
            $stmt->bindParam(2, $table, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetch();
            if ($results) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->type == self::TYPE_SQLITE) {
            $query = 'SELECT name FROM sqlite_master WHERE type=\'table\' AND name=?;';
            $stmt = $this->connector->prepare($query);
            $stmt->bindParam(1, $table, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            if (count($results > 0)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new DatabaseException(
                $this,
                __CLASS__.'->'.__METHOD__.'(): invalid database type in object.',
                DatabaseException::EXCEPTION_CORRUPTED_OBJECT
            );
        }

    }

}

?>


?>
