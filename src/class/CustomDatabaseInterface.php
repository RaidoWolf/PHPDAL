<?

interface CustomDatabaseInterface extends DatabaseInterface {

    public function __construct ( //constructor
        $name//,
        //$user,
        //$pass,
        //$host,
        //$port,
        //$table
    ); //only DBMS servers require last few variables, but implement them in order please

}

?>
