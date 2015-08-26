<?php

require_once __DIR__.'/class/CrossDatabaseInterface.php';       //Cross-DBMS Interface
require_once __DIR__.'/class/DatabaseConditionInterface.php';   //Database Condition Interface
require_once __DIR__.'/class/DatabaseExceptionInterface.php';   //Database Exception Interface
require_once __DIR__.'/class/DatabaseExceptionModel.php';       //Database Exception Model
require_once __DIR__.'/class/DatabaseInterface.php';            //Database Class Interface
require_once __DIR__.'/class/DatabaseModel.php';                //Database Class Model
require_once __DIR__.'/class/DatabaseUtils.php';                //Database Utilities (static)
require_once __DIR__.'/class/MySQLDatabase.php';                //MySQL Database Class
require_once __DIR__.'/class/MySQLException.php';               //MySQL Exception Class
require_once __DIR__.'/class/PDOE.php';                         //PDO Wrapper Class (stats/diagnostics)
require_once __DIR__.'/class/PDOEStatement.php';                //PDOStatement Wrapper class (stats/diagnostics)
require_once __DIR__.'/class/PostgreSQLDatabase.php';           //PostgreSQL Database Class
require_once __DIR__.'/class/PostgreSQLException.php';          //PostgreSQL Exception Class
require_once __DIR__.'/class/SQLiteDatabase.php';               //SQLite3 Database Class
require_once __DIR__.'/class/SQLiteException.php';              //SQLite3 Exception Class

?>
