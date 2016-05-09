<?php

/**
 * PHPDAL - PHP Database Abstraction Library
 * =========================================
 *
 * INSTRUCTIONS: include/require this file in your project and you're ready to roll.
 *
 * @package PHPDAL
 * @author Alexander Barber
 */

require_once __DIR__.'/class/DatabaseInterface.php';                //Database Class Interface
require_once __DIR__.'/class/CrossDatabaseInterface.php';           //Cross-DBMS Interface
require_once __DIR__.'/class/CustomDatabaseInterface.php';          //DBMS Database Interface
require_once __DIR__.'/class/DatabaseModel.php';                    //DBMS Database Model
require_once __DIR__.'/class/Database.php';                         //Cross-DBMS Class
require_once __DIR__.'/class/DatabaseConditionInterface.php';       //Database Condition Interface
require_once __DIR__.'/class/DatabaseConditionModel.php';           //Database Condition Model
require_once __DIR__.'/class/DatabaseConditionGroupInterface.php';  //Database Condition Group Interface
require_once __DIR__.'/class/DatabaseConditionGroupModel.php';      //Database Condition Group Model
require_once __DIR__.'/class/DatabaseExceptionInterface.php';       //Database Exception Interface
require_once __DIR__.'/class/DatabaseException.php';                //Cross-DBMS Exception (and Database Exception Model)
require_once __DIR__.'/class/DatabaseState.php';                    //Database State/Schema/Structure Manager Class
require_once __DIR__.'/class/DatabaseStatement.php';                //Database Prepared Statements Class
require_once __DIR__.'/class/SchemaInterface.php';                  //Database Schema Interface
require_once __DIR__.'/class/SchemaModel.php';                      //Database Schema Model
require_once __DIR__.'/class/DatabaseGrammarInterface.php';         //Database Grammar Interface
require_once __DIR__.'/class/DatabaseGrammarModel.php';             //Database Grammar Model
require_once __DIR__.'/class/DatabaseUtils.php';                    //Database Utilities (static)

/*
 * The following are require_once'd on database instantiation:
 * (and thus, do not need to be included here)
 * -----------------------------------------------------------
 * require_once __DIR__.'/class/MySQLCondition.php';                //MySQL-specific Condition Class
 * require_once __DIR__.'/class/MySQLConditionGroup.php';           //MySQL-specific Condition Group Class
 * require_once __DIR__.'/class/MySQLGrammar.php';                  //MySQL-specific Grammar (static) Class
 * require_once __DIR__.'/class/MySQLDatabase.php';                 //MySQL-specific Database Class
 * require_once __DIR__.'/class/PostgreSQLCondition.php';           //PostgreSQL-specific Condition Class
 * require_once __DIR__.'/class/PostgreSQLConditionGroup.php';      //PostgreSQL-specific Condition Group Class
 * require_once __DIR__.'/class/PostgreSQLGrammar.php';             //PostgreSQL-specific Grammar (static) Class
 * require_once __DIR__.'/class/PostgreSQLDatabase.php';            //PostgreSQL-specific Database Class
 * require_once __DIR__.'/class/SQLiteCondition.php';               //SQLite3-specific Condition Class
 * require_once __DIR__.'/class/SQLiteConditionGroup.php';          //SQLite3-specific Condition Group Class
 * require_once __DIR__.'/class/SQLiteGrammar.php';                 //SQLite3-specific Grammar (static) Class
 * require_once __DIR__.'/class/SQLiteDatabase.php';                //SQLite3-specific Database Class
 */

?>
