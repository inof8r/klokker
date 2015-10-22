<?php
/**
* DBConnector
*
* http://www.inof8.nl
*
* Generic database connection class
*
* IMPORTANT NOTE
* there is no warranty, implied or otherwise with this software.
* 
*
* @author		Joey Delemarre <info@inof8.nl>
* @version 	001
* @package	DBConnector
*/
class DBConnector {
	var $dbServer="mysql";
	var $host = "localhost";
   	var $db = "";
   	var $user = "";
   	var $pass = "";


	function DBConnector ($host, $db, $user, $pass){
		if ($host != "") {
		$this->host = $host;
		}
   		if ($db != "") {
   			$this->db = $db;
		}
   		if ($user != "") {
	   		$this->user = $user;
   		}
   		if ($pass != "") {
   			$this->pass = $pass;
   		}
   		$this->link = mysql_connect($this->host, $this->user, $this->pass) or die (mysql_error());;
   		mysql_select_db($this->db);
	}
	
	function select_db($dbname){

   		$this->link = mysql_pconnect($this->host, $this->user, $this->pass) or die (mysql_error());;


		$this->db = $dbname;
		$db_selected = mysql_select_db($this->db, $this->link);	
	}
	function run_query($query){
	// escape string (sql injection precaution)
		mysql_real_escape_string($query);

		$result = mysql_query($query);
//		print "------ running query $query ------<br>";
		if ($result) {
			$rows = Array();
			if ($result != 0) {
			while($row = @mysql_fetch_array($result, MYSQL_ASSOC)) {

				$cur_row = Array();
				$total = count($row);
				foreach($row as $col => $val) {
//					print "$col => $val<br>";					
//					if ($col != $oldcol) {
						$cur_row[$col] = $val;
						$oldcol = $col;
//					}
				}
				$rows[] = $cur_row;
			}
			
			if (substr($query,0,6) == "INSERT") {
				return mysql_insert_id();
		
			} else {
				return $rows;
			}
			}
		}
	}
	

} // end of class

?>