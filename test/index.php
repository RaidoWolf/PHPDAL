<?php

//includes
require_once __DIR__.'/PHPDAL.php'; //make sure to copy the library like so

session_start(); //begin session

//pull in variables
if (isset($_POST['type']) || isset($_SESSION['type'])) {
    $type = isset($_POST['type']) ? $_POST['type'] : $_SESSION['type'];
} else {
    $type = null;
}
if (isset($_POST['name']) || isset($_SESSION['name'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : $_SESSION['name'];
} else {
    $name = null;
}
if (isset($_POST['user']) || isset($_SESSION['user'])) {
    $user = isset($_POST['user']) ? $_POST['user'] : $_SESSION['user'];
} else {
    $user = null;
}
if (isset($_POST['pass']) || isset($_SESSION['pass'])) {
    $pass = isset($_POST['pass']) ? $_POST['pass'] : $_SESSION['pass'];
} else {
    $pass = null;
}
if (isset($_POST['host']) || isset($_SESSION['host'])) {
    $host = isset($_POST['host']) ? $_POST['host'] : $_SESSION['host'];
} else {
    $host = null;
}
if (isset($_POST['port']) || isset($_SESSION['port'])) {
    $port = isset($_POST['port']) ? $_POST['port'] : $_SESSION['port'];
} else {
    $port = null;
}
if (isset($_POST['table']) || isset($_SESSION['table'])) {
    $table = isset($_POST['table']) ? $_POST['table'] : $_SESSION['table'];
} else {
    $table = null;
}
if (isset($_POST['submit'])) {
    $submit = true;
} else {
    $submit = false;
}

//on submit
if ($submit) {
    //save to session
    if (isset($type) && $type != null) {
        $_SESSION['type'] = $type;
    }
    if (isset($name) && $name != null) {
        $_SESSION['name'] = $name;
    }
    if (isset($user) && $user != null) {
        $_SESSION['user'] = $user;
    }
    if (isset($pass) && $pass != null) {
        $_SESSION['pass'] = $pass;
    }
    if (isset($host) && $host != null) {
        $_SESSION['host'] = $host;
    }
    if (isset($port) && $port != null) {
        $_SESSION['port'] = $port;
    }
    if (isset($table) && $table != null) {
        $_SESSION['table'] = $table;
    }
}

//if required fields are set, try to create a database object
if (isset($type) && isset($name)) {
    try {
        $db = new Database($type, $name, $user, $pass, $host, $port, $table);
    } catch (DatabaseException $e) {
        //do something with the error
    }
}

if (isset($db)) {
    try {
        //try your stuff here
    } catch (DatabaseException $e) {
        //do something with the error
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PHPDAL Database Manager</title>
    <meta charset="utf-8" />
    <style>
        .form {
            background:rgba(0,0,0,0.75);
            border-radius:10px;
            color:#ffffff;
            display:inline-block;
            padding:5px;
        }
    </style>
</head>
<body>
    <form method="post">
        <div class="form">
            type:
            <select name="type" id="form-type">
                <option value="0" id="form-type-none">-- SELECT ONE ---</option>
                <option value="<?php echo Database::type_MYSQL ?>" id="form-type-mysql">MySQL</option>
                <option value="<?php echo Database::type_PGSQL ?>" id="form-type-pgsql">PostgreSQL</option>
                <option value="<?php echo Database::type_SQLITE ?>" id="form-type-sqlite">SQLite</option>
            </select>
        </div>
        <div class="form">
            Name:
            <input type="text" name="name" id="form-name" />
        </div>
        <div class="form">
            type:
            <input type="text" name="type" id="form-type" />
        </div>
        <div class="form">
            Password:
            <input type="password" name="pass" id="form-pass" />
        </div>
        <div class="form">
            Hostname:
            <input type="text" name="host" id="form-host" />
        </div>
        <div class="form">
            Port:
            <input type="text" name="port" id="form-port" />
        </div>
        <div class="form">
            Table:
            <input type="text" name="table" id="form-table" />
        </div>
        <div class="form">
            <input type="submit" name="submit" value="submit" />
        </div>
    </form>
</body>
</html>
