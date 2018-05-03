<?php
/**
 * @brief Interface to for the CommonMethods.php file. Include this file in your code instead
 * of the CommonMethods.php file.
 */

include_once "./CommonMethods.php";

interface CommonInterface
{
    /**
     * Common constructor.
     * @param $debug
     *
     * The constructor for the Common class
     */
    public function Common($debug);

    /**
     * @param $db
     *
     * Uses the $db class member functions to actually make the connection to the database
     */
    public function connect($db);

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
    public function executeQuery($sql, $filename);
}