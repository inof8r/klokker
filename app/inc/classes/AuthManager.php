<?php
session_start();
/**
* AuthManager
*
* http://www.inof8.nl
*
* Authentication service
*
* IMPORTANT NOTE
* there is no warranty, implied or otherwise with this software.
* 
*
* @author		Joey Delemarre <info@inof8.nl>
* @version 	001
* @package	DBConnector
*/
class AuthManager {
	var $crudEngine="";
   	var $userTable = "";
   	var $userField = "";
   	var $passField = "";
   	var $sessionField = "";
   	var $dateField = "";
   	var $passEncryptor = "md5";

	function AuthManager ($crud, $userTable, $userField, $passField, $sessionField="ses",$dateField="dtime"){
		if ($crud != "") {
		$this->crudEngine = $crud;
		}
   		if ($userTable != "") {
   			$this->userTable = $userTable;
		}
   		if ($userField != "") {
	   		$this->userField = $userField;
   		}
   		if ($passField != "") {
   			$this->passField = $passField;
   		}
   		if ($sessionField != "") {
   			$this->sessionField = $sessionField;
   		}
   		if ($dateField != "") {
   			$this->dateField = $dateField;
   		}

	}
	function updateSession($userid){
		
        $str                    = "WHERE id='$userid'";
		$newsession             = md5(time() . " -" . $username);
		$fields = Array($this->sessionField,$this->dateField);
		$values = Array("$newsession",date("Y:m:d H:i:s", time()));		
        $updateSession = $this->crudEngine->update("busibox_accounts", $fields, $values, "$str");        
		return $newsession;
		
	}
	function authSession($sessionID){
//		$this->crudEngine->enableTracing(1);
		$sesResult = $this->crudEngine->read($this->userTable, "WHERE " . $this->sessionField ."='" . $sessionID . "'", "*");
		
		if ($sesResult[0][$this->sessionField] == $sessionID) {
			
			return "1";			
		} else {
			
			return "0";
		}

		
	}
	function destroySession($sessionID){	
//		$this->crudEngine->enableTracing(1);	
        $str                    = "WHERE " . $this->sessionField . "='$sessionID'";
		$newsession             = "*";
		$fields = Array($this->sessionField);
		$values = Array("$newsession");		
        $updateSession = $this->crudEngine->update("busibox_accounts", $fields, $values, "$str");        
		$_SESSION["BSBX_SES"] = "";
		return $newsession;
	

	}	
	

	function encryptPass($password){
		if ($this->passEncryptor == "md5") {
			$encPass = md5($password);
		}
		return $encPass;
	}	
	function auth_user($username, $password){

		$encPass = $this->encryptPass($password);
		//$this->crudEngine->enableTracing(1);
		$LoginResult = $this->crudEngine->read($this->userTable, "WHERE " . $this->userField ."='" . $username . "' AND " . $this->passField . "='" . $encPass . "'", "*");
		if ($LoginResult[0]["username"] == $username) {
			$userid = $LoginResult[0]["id"];
			$ses = $this->updateSession($userid);
			$result["result"] = "success";
			$result["session"] = "$ses";			
			$result["userid"] = "$userid";
			$result["username"] = "$username";
			$result["authorized"] = "1";			
		} else {
			$result["result"] = "failed";
			$result["session"] = "";			
			$result["userid"] = "";
			$result["username"] = "";
			$result["authorized"] = "0";			
		}
        $_SESSION["BSBX_SES"]   = $ses;
        $_SESSION["BSBX_UID"]   = $userid;
        $_SESSION["BSBX_UNAME"] = $username;
		
		$result = json_encode($result);
		return $result;
	}
	
	

} // end of class

?>