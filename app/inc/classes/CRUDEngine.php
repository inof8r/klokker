<?php
/**
* CRUDEngine
*
* http://www.inof8.nl
*
* Create / Read / Update / Delete Engine
*
* IMPORTANT NOTE
* there is no warranty, implied or otherwise with this software.
* 
*
* @author		Joey Delemarre <info@inof8.nl>
* @version 	002
* @package	CRUDEngine
*/
class CRUDEngine {
	var $prevDatabase="";
	var $curDatabase="";
	var $curTable="";
	var $curHost="";	
	var $tracingEnabled= 0;
	var $sessionLog=Array();	
	function CRUDEngine ($host, $db, $user, $pass){
		$this->db = new DBConnector($host, $db, $user, $pass);    
		$this->curHost = $host;   
		$this->curDatabase = $db;		
		$this->prevDatabase = $db;
	}

	function enableTracing($val){
		$this->tracingEnabled = $val;
	}

	function changeDatabaseConnection($dbname, $user, $pass){
		$this->db = new DBConnector($this->curHost, $db, $user, $pass);       
		if($this->tracingEnabled == 1) {
			print "Database connection changed, $dbname selected<br>";		
		}
		$this->curDatabase = $dbname;

		
		$this->db->select_db($dbname);
	}



	function changeDatabase($dbname){

		if($this->tracingEnabled == 1) {
			print "Database $dbname selected<br>";		
		}

		$this->db->select_db($dbname);
		$this->curDatabase = $dbname;		
	}

	function getDatabaseName(){
		return $this->curDatabase;

	}

	
	function create($table, $fields, $values, $str){
		$the_fields = $this->formatFields($fields);
		$the_values = $this->formatValues($values);		
		$thequery = "INSERT INTO $table ($the_fields) VALUES (" . $the_values . ") $str";
		$this->sessionLog[] = "CREATE : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}

		return $this->db->run_query($thequery);
		
	}	

	function read($table, $str, $fields){
		$field_selection = $this->formatFields($fields);
		$thequery = "SELECT $field_selection FROM $table $str";
		$this->sessionLog[] = "READ : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}
		return $this->db->run_query($thequery);
	}
	
	function query($str){

		$thequery = "$str";
		$this->sessionLog[] = "READ : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}
		return $this->db->run_query($thequery);
	}
	
	function update($table, $fields, $values, $str){
		for($i=0 ; $i < count($fields); $i++) {

			$cur_fname = $fields[$i];
			$cur_value = $values[$i];			
//			print "cur_fname = $cur_fname - cur_value = $cur_value<br>";
			$update_string .= "" . $cur_fname . "='" . $cur_value ."',";	
//			print "update_string = $update_string <br>";			
		}
		$update_string = substr($update_string,0,strlen($update_string)-1);
		$thequery = "UPDATE $table SET $update_string $str";
		$this->sessionLog[] = "UPDATE : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}

		return $this->db->run_query($thequery);
		
	}	

	function delete($table, $str){
		$thequery = "DELETE FROM $table $str";
		$this->sessionLog[] = "DELETE : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}
	
		return $this->db->run_query($thequery);
	}

	function customQuery($thequery){

		$this->sessionLog[] = "CUSTOM : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}

		return $this->db->run_query($thequery);
	}
	
	function showcolumns($table, $str){
		$thequery = "SHOW COLUMNS FROM $table $str";
		$this->sessionLog[] = "SHOW COLUMNS : $thequery";
		if($this->tracingEnabled == 1) {
			print "$thequery <br>";		
		}
	
		return $this->db->run_query($thequery);
	}

	function formatFields($fields){
		
		if ($fields != "") {
			if (gettype($fields) == "array") {
				$field_selection = implode(",",$fields);
			} else {
				$field_selection = "$fields";
			}
		} else {
			$field_selection = "*";
		}
		return $field_selection;
	}
	
	function formatValues($values){
		$field_selection = "";
		if ($values != "") {
			if (gettype($values) == "array") {
				for($i=0 ; $i < count($values) ; $i++) {
					$field_selection .= "'$values[$i]',";				
				}
				$field_selection = substr($field_selection,0,strlen($field_selection)-1);
			} else {
				$field_selection = "$values";
			}
		} else {
			$field_selection = "\"\"";
		}
		return $field_selection;
	}	
	
	function getLogEntry($command){
		if($this->tracingEnabled == 1) {
			$theMessage = $this->sessionLog[count($this->sessionLog)-1];
			print "Getting log entry ($command): $theMessage<br>";		
		}
		
		return $this->sessionLog[count($this->sessionLog)-1];
	}


} // end of class

?>