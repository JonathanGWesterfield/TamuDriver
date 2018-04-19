<?php 
/*****************************************
** File:    CommonMethods.php
** Project: CSCE 315 Project 1, Spring 2018
** Author:  XXXXXXXXXXX
** Date:    2/2/18
** Section: 505
** E-mail:  XXXXXXXXXXX
**
**   This file contains code for Project 1 to connect the web site
**   to the database.
**   This file creates a new Common object, connects to the database using 
**   the object, and executes queries.
**
***********************************************/

class Common
{	
	var $conn;
	var $debug;
	
	var $db="database.cs.tamu.edu";  // name of the database server
	var $dbname="XXXXXXXXXXX";      // name of the database
	var $user="XXXXXXXXXXX";        // username to log in to database
	var $pass="XXXXXXXXXXX";          // password to log in to database
		
	// Common
	// Connect to database		
	function Common($debug)
	{
		$this->debug = $debug; 
		$rs = $this->connect($this->user); 
		return $rs;
	}

	// connect
	// Connect to MySQL DB server	
	function connect($db)
	{
		try
		{
			$this->conn = new PDO('mysql:host='.$this->db.';dbname='.$this->dbname, $this->user, $this->pass);
	    	} catch (PDOException $e) {
        	    print "Error!: " . $e->getMessage() . "<br/>";
	            die();
        	}
	}

	// executeQuery
	// Executes specified query 
	function executeQuery($sql, $filename) 
	{
		if($this->debug == true) { echo("$sql <br>\n"); }
		$rs = $this->conn->query($sql) or die("Could not execute query '$sql' in $filename"); 
		return $rs;
	}			

} 

?>