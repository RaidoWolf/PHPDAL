<?

interface CustomDatabaseInterface extends DatabaseInterface {

    //const UPPER_LIMIT = 18446744073709551615; //set this to the maximum value of LIMIT

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
