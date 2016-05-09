<?php

final class Database implements CrossDatabaseInterface {

    // -- PROPERTIES/MEMBERS -- //

    protected $child;
    protected $config;
    protected $encoding;
    protected $host;
    protected $name;
    protected $open;
    protected $port;
    protected $table;
    protected $type;

    // -- CONSTANTS/FLAGS -- //

    const FIELD_DATA = 0;
    const FIELD_TABLE = 1;
    const FIELD_COLUMN = 2;

    const KEYWORD_NONE = 0;
    const KEYWORD_ALL = 1;

    const SORT_NONE = 0;
    const SORT_ASC = 1;
    const SORT_DESC = 2;

    const TYPE_MYSQL = 1;
    const TYPE_PGSQL = 2;
    const TYPE_SQLITE = 3;

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
        $type,
        $name,
        $user   = null,
        $pass   = null,
        $host   = 'localhost',
        $port   = null,
        $table  = null
    ) {

        /*
         * Input Handling for $type (database type)
         *
         * $type is required, must be a string, and must be one of the following list of
         * valid strings: mysql, pgsql, or sqlite.
         */
        if (isset($type)) {
            //type is set
            $typeof_type = gettype($type);
            if ($typeof_type == 'integer') {
                //type is a string
                if (!in_array($type, [
                    self::TYPE_MYSQL,
                    self::TYPE_PGSQL,
                    self::TYPE_SQLITE
                ])) {
                    //type is not in the list of valid types
                    throw new DatabaseException(
                        $this,
                        'Database->__construct() failed due to invalid database type given.',
                        DatabaseException::EXCEPTION_INVALID_DATABASE_TYPE
                    );
                }
            } elseif ($typeof_type == 'string') {
                $lowerType = strtolower($type);
                if ($lowerType == 'mysql') {
                    $type = self::TYPE_MYSQL;
                } elseif ($lowerType == 'pgsql') {
                    $type = self::TYPE_PGSQL;
                } elseif ($lowerType == 'sqlite') {
                    $type = self::TYPE_SQLITE;
                } else {
                    throw new DatabaseException(
                        $this,
                        'Database->__construct() failed due to invalid database type (string) given.',
                        DatabaseException::EXCEPTION_INVALID_DATABASE_TYPE
                    );
                }
            } else {
                //type is not an integer
                throw new DatabaseException(
                    $this,
                    'Database->__construct() failed due to type argument of invalid data type.',
                    DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
                );
            }
        } else {
            //type is not set
            throw new DatabaseException(
                $this,
                'Database->__construct() failed due to missing type argument.',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
        }

        // -- Instantiation of Child Object (MySQLDatabase, PostgreSQLDatabase, or SQLiteDatabase) -- //
        if ($type == self::TYPE_MYSQL) {
            require_once __DIR__.'/MySQLCondition.php'; //load MySQL condition class
            require_once __DIR__.'/MySQLConditionGroup.php'; //load MySQL condition group class
            require_once __DIR__.'/MySQLDatabase.php'; //load MySQL database class
            $this->child = new MySQLDatabase($name, $user, $pass, $host, $port, $table);
        } elseif ($type == self::TYPE_PGSQL) {
            require_once __DIR__.'/PostgreSQLCondition.php'; //load PostgreSQL condition class
            require_once __DIR__.'/MySQLConditionGroup.php'; //load PostgreSQL condition group class
            require_once __DIR__.'/PostgreSQLDatabase.php'; //load PostgreSQL database class
            $this->child = new PostgreSQLDatabase($name, $user, $pass, $host, $port, $table);
        } elseif ($type == self::TYPE_SQLITE) {
            require_once __DIR__.'/SQLiteCondition.php'; //load SQLite3 condition class
            require_once __DIR__.'/SQLiteConditionGroup.php'; //load SQLite condition group class
            require_once __DIR__.'/SQLiteDatabase.php'; //load SQLite3 database class
            $this->child = new SQLiteDatabase($name, $table);
        } else {
            throw new DatabaseException (
                $this,
                'Database->__construct() failed due to invalid database type given',
                DatabaseException::EXCEPTION_INPUT_INVALID_TYPE
            );
        }

        //Set class properties/members
        $this->type     = $type;
        $this->name     = $name;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->host     = $host;
        $this->port     = $port;
        $this->table    = $table;

    }

    /**
     * Invocation Method
     *
     * @see DatabaseInterface::__invoke()
     */
    public function __invoke () {

        return $this->child->__invoke();

    }

    /**
     * String Conversion Method
     *
     * @see DatabaseInterface::__toString()
     */
    public function __toString () {

        return $this->child->__toString();

    }

    public function columnConform ($table, $column) {

        return $this->child->columnConform($table, $column);

    }

    /**
     * Database->columnExists() Method
     *
     * Tests if a given column exists in a given table.
     *
     * @param string $column (required) - name of column for which to test
     * @param string $table (optional) - name of table, unless table predefined
     * @return boolean - true if column exists, false if not
     * @see DatabaseInterface::columnExists()
     */
    public function columnExists ($column, $table = null) {

        return $this->child->columnExists($column, $table);

    }

    public function columnMatches ($column, $table = null) {

        return $this->child->columnMatches($column, $table);

    }

    /**
     * Database->delete() Method
     *
     * Deletes rows matching given condition array
     *
     * @param  array  $condition (required) - conditioon array passed to DBMS condition object
     * @param  int    $start     (optional) - offset at which to start deleting
     * @param  int    $limit     (optional) - limit number of rows to affect
     * @param  string $table     (optional) - name of table, not needed if table is predefined
     * @return bool              returns true on success, false on failure
     * @throws DatabaseException if someone dun goofed
     */
    public function delete ($condition, $start = null, $limit = null, $table = null) {

        return $this->child->delete($condition, $start, $limit, $table);

    }

    /**
     * DatabaseException->exec() Method
     *
     * Prepare and execute a statement. This method is recommended for single-
     * use queries. It is optimized by checking a table of MD5 hashes of
     * existing prepared statements. Regardless of this optimization, for any
     * queries that will be run multiple times, it is strongly recommended that
     * you use the DatabaseStatement->exec() method. Using that method, there
     * will need to be no MD5 checksums, hash table checks, string comparisons,
     * or compilation. This is provided only for convenience.
     *
     * @param  string $query      The statement to prepare for execution
     * @param  array  $args       Array of arguments to the given statement
     * @param  array  $tables     Array of table values to insert
     * @param  array  $columns    Array of column values to insert
     * @param  array  $sets       Array of set values to insert
     * @param  array  $tablesets  Array of table set values to insert.
     * @param  array  $columnsets Array of column set values to insert.
     * @param  array  $conditions Array of DatabaseCondition objects to insert.
     * @param  int    $action     ID of action to execute on final data. Recommend using constants for this.
     * @param  array  $actionargs Array representing data to give to action using $val keys.
     * @return array              Array of data returned from the execution.
     */
    public function exec (
        $query,
        $args       = [],
        $tables     = [],
        $columns    = [],
        $sets       = [],
        $tablesets  = [],
        $columnsets = [],
        $conditions = [],
        $action     = DatabaseModel::ACTION_NONE,
        $actionargs = []
    ) {
        return $this->child->exec(
            $query,
            $args,
            $tables,
            $columns,
            $sets,
            $tablesets,
            $columnsets,
            $conditions,
            $action,
            $actionargs
        );
    }

    public function getAllSchemas () {

        return $this->child->getAllSchemas();

    }

    /**
     * Database->getChild() Method
     *
     * Getter for mutable reference to child object (MySQLDatabase,
     * PostgreSQLDatabase, or SQLiteDatabase).
     *
     * @return &DatabaseModel - child database object
     * @see CrossDatabaseInterface::getChild()
     */
    public function &getChild () {

        return $this->child;

    }

    /**
     * Database->getColumns() Method
     *
     * Getter for array of all columns in a given table.
     *
     * @param string $table (optional) - name of table, unless table predefined
     * @return array - array of columns in the table
     * @see DatabaseInterface::getColumns()
     */
    public function getColumns ($table = null) {

        return $this->child->getColumns($table);

    }

    public function getConnector () {

        return $this->child->getConnector();

    }

    public function getCreateQuery () {

        return $this->child->getCreateQuery();

    }

    public function getDatabaseName () {

        return $this->child->getDatabaseName();

    }

    /**
     * Database->getDefaultTable() Method
     *
     * Getter for default table defined in the constructor or Database->setTable().
     *
     * @return string - default table (value of $this->child->table)
     * @see DatabaseInterface::getDefaultTable()
     */
    public function getDefaultTable () {

        return $this->child->getDefaultTable();

    }

    public function getHostname () {

        return $this->child->getHostname();

    }

    public function getPortNumber () {

        return $this->child->getPortNumber();

    }

    public function getSchema ($name) {

        return $this->child->getSchema($name);

    }

    /**
     * Database->getTables() Method
     *
     * Getter for array of tables in the database.
     *
     * @return array - array of tables in the database
     * @see DatabaseInterface::getTables()
     */
    public function getTables () {

        return $this->child->getTables();

    }

    /**
     * Database->getType() Method
     *
     * Getter for database type.
     *
     * @return string database type, value of $this->type
     * @see DatabaseInterface::getType()
     */
    public function getType () {

        return $this->type;

    }

    /**
     * Database->hasDefaultTable() Method
     *
     * Checks if default table (defined in constructor or Database->setTable) is defined.
     *
     * @return boolean - true if default table is defined, false if not
     * @see DatabaseInterface::hasDefaultTable()
     */
    public function hasDefaultTable () {

        if (isset($this->table) && $this->table != null && $this->child->hasDefaultTable()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Database->insert() Method
     *
     * Insert new data into the database.
     * This method is a chainable mutator.
     *
     * @param array $in (required) - associative array of input to be inserted, keys being the name of columns
     * @param string $table (optional if defined in constructor) - table to use
     * @return Database - reference to self
     * @see DatabaseInterface::insert()
     */
    public function insert ($in, $table = null) {

        return $this->child->insert($in, $table);

    }

    public function schemaConform () {

        return $this->child->schemaConform();

    }

    public function schemaMatches () {

        return $this->child->schemaConform();

    }

    /**
     * Database->select() Method
     *
     * @param array|string $columns (optional) - columns to return. if left empty/null, will default to all columns (*)
     * @param DatabaseCondition|array|string $conditions (optional) - conditions to lookup. If left empty/null, will default to no conditions.
     * @param int $start (optional) - starting index from which to begin selecting. If left empty, defaults to 0.
     * @param int $count (optional) - maximum number of results to return. If left empty, defaults to no limit.
     * @param string $sortBy (optional) - column with which to sort the table. If left empty, defaults to the first column in the table.
     * @param int $sortDirection (optional - (uses flags) direction to sort the table. If left empty, defaults to none. Options are SORT_NONE, SORT_ASC, SORT_DESC
     * @param string $table (optional if table set in constructor) - table from which to select.
     * @return array - results of the select query as an associative array.
     */
    public function select (
        $columns = ['*'],
        $conditions = null,
        $start = null,
        $count = null,
        $sortBy = null,
        $sortDirection = null,
        $table = null
    ) {

        return $this->child->select(
            $columns,
            $conditions,
            $start,
            $count,
            $sortBy,
            $sortDirection,
            $table
        );

    }

    public function tableConform ($table = null) {

        return $this->child->tableConform($table);

    }

    /**
     * Database->tableExists() Method
     *
     * Checks if a given table exists in the database
     *
     * @param string $table (required) - table for which to check
     * @return boolean - true if table exists, false if not
     * @see DatabaseInterface::tableExists()
     */
    public function tableExists ($table) {

        return $this->child->tableExists($table);

    }

    public function tableMatches ($table = null) {

        return $this->child->tableMatches($table);

    }

    public function triggerConform ($trigger) {

        return $this->child->triggerConform($trigger);

    }

    public function triggerMatches ($trigger) {

        return $this->child->triggerMatches($trigger);

    }

    public function viewConform ($view) {

        return $this->child->viewConform($view);

    }

    public function viewMatches ($view) {

        return $this->child->viewMatches($view);

    }

}

?>
