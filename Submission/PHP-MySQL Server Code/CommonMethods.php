<?php
/**
 * Common is responsible for actually connecting to the MySQL database. The Common class object must be created
 * and the connect() function run before any functions on the database can be performed.
 */

class Common implements CommonInterface
{
    var $conn;
    var $debug;



    var $db="database.cse.tamu.edu";
    var $dbname="XXXXX-TamuDriver";
    var $user="XXXXX";
    var $pass="Whoop19!";

    /**
     * Common constructor.
     * @param $debug
     *
     * The constructor for the Common class
     */
    function Common($debug)
    {
        $this->debug = $debug;
        $rs = $this->connect($this->user); // db name really here
        return $rs;
    }

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% */

    /**
     * @param $db
     *
     * Uses the $db class member functions to actually make the connection to the database
     */
    function connect($db)// connect to MySQL DB Server
    {
        try
        {
            $this->conn = new PDO('mysql:host='.$this->db.';dbname='.$this->dbname, $this->user, $this->pass);
        }
        catch (PDOException $e)
        {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% */

    /**
     * @param $sql
     * @param $filename
     * @return mixed
     *
     * Uses the sql statement that is passed in and executes the query
     *
     * Example Code: <br>
     * $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData"; <br>
     * $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]); <br>
     * $row = $rs->fetch(PDO::FETCH_ASSOC); <br>
     * return (int)$row['COUNT(WalkerNumber)']; <br>
     */
    function executeQuery($sql, $filename) // execute query
    {
        /*if($this->debug == true)
        {
            echo("<br>$sql <br>\n");
        }*/
        $rs = $this->conn->query($sql) or die("Could not execute query '$sql' in $filename");
        return $rs;
    }

} // ends class, NEEDED!!

?>
