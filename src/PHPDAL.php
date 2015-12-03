<?php

/**
 * PHPDAL - PHP Database Abstraction Library
 * =========================================
 *
 * PHPDAL is a PHP Database Abstraction Library designed to provide a very high
 * level interface to databases using purely object oriented code. The main goals
 * of this library over similar libraries are the following:
 *
 * Simplicity - A database abstraction library should be easier to use than native
 *     database libraries or SQL queries.
 *
 * Speed - Runtime-translation of SQL queries using expensive regex operations is
 *     a bad practice. This library handles data using pre-written templates, basic
 *     string concatenation, otherwise you will have to pre-write a query for each
 *     language you need. This means better performance.
 *
 * Maintainability - This library is meant to be modular, easily configurable, and
 *     easy to develop. There is are focuses on limiting redundant code and enforcing
 *     a clearly defined standard. Practices used to ensure these focuses are met
 *     include using object oriented code, strict MVC separation, use of interfaces,
 *     and use of model or prototype classes and extending them for specific cases.
 *
 * INSTRUCTIONS: include/require this file in your project and you're ready to roll.
 *
 * @package PHPDAL
 * @author Alexander Barber
 */

require_once __DIR__.'/class/DatabaseInterface.php';            //Database Class Interface
require_once __DIR__.'/class/CrossDatabaseInterface.php';       //Cross-DBMS Interface
require_once __DIR__.'/class/CustomDatabaseInterface.php';      //DBMS Database Interface
require_once __DIR__.'/class/DatabaseModel.php';                //DBMS Database Model
require_once __DIR__.'/class/Database.php';                     //Cross-DBMS Class
require_once __DIR__.'/class/DatabaseConditionInterface.php';   //Database Condition Interface
require_once __DIR__.'/class/DatabaseConditionModel.php';       //Database Condition Model
require_once __DIR__.'/class/DatabaseExceptionInterface.php';   //Database Exception Interface
require_once __DIR__.'/class/DatabaseException.php';            //Cross-DBMS Exception (and Database Exception Model)
require_once __DIR__.'/class/DatabaseState.php';                //Database State/Schema/Structure Manager Class
require_once __DIR__.'/class/DatabaseStatement.php';            //Database Prepared Statements Class
require_once __DIR__.'/class/DatabaseUtils.php';                //Database Utilities (static)

/*
 * The following are require_once'd on database instantiation:
 * (and thus, do not need to be included here)
 * -----------------------------------------------------------
 * require_once __DIR__.'/class/MySQLCondition.php';
 * require_once __DIR__.'/class/MySQLDatabase.php';
 * require_once __DIR__.'/class/PostgreSQLCondition.php';
 * require_once __DIR__.'/class/PostgreSQLDatabase.php';
 * require_once __DIR__.'/class/SQLiteCondition.php';
 * require_once __DIR__.'/class/SQLiteDatabase.php';
 */

?>
