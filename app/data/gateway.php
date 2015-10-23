<?php
session_start();

include("../inc/config.php");
$tagId = $_GET["tagId"];
$mode = $_GET["mode"];
if ($_POST["mode"] != "") {
	$mode = $_POST["mode"];
}

$AdmincrudEngine->changeDatabase($main_db_name);
$AuthManagerService = new AuthManager($AdmincrudEngine, "busibox_accounts", "username", "passwd");
	

if ($mode == "login") {
	$username = $_POST["username"];
	$password = $_POST["password"];
	$result = $AuthManagerService->auth_user($username, $password);

	$data = json_decode($result);	
	print $result;
	exit;
}

if ($mode == "logout") {
$deAuthorize = $AuthManagerService->destroySession($_SESSION["BSBX_SES"]);

	$_SESSION["BSBX_UID"] = "";
	$_SESSION["BSBX_UNAME"] = "";
	$_SESSION["BSBX_SES"] = "";

	$result["userid"] = $_SESSION["BSBX_UID"];
	$result["username"] = $_SESSION["BSBX_UNAME"];
	$result["session"] = $_SESSION["BSBX_SES"];
	$result["authorized"] = "0";
	$result["result"] = "success";
	$data = json_encode($result);
	print $data;
	exit;
}

$AdmincrudEngine->changeDatabase($main_db_name);
$isAuthorized = $AuthManagerService->authSession($_SESSION["BSBX_SES"]);
$UserisAuthorized = 0;
//print "<br>isAuthorized = $isAuthorized";

if ($isAuthorized == "1") {
	$UserisAuthorized = 1;
}
if ($_SESSION["BSBX_SES"] == "") {
	$UserisAuthorized = 0;
}

if ($mode == "home") {
	$returnvals[0]["userid"] = $_SESSION["BSBX_UID"];
	$returnvals[0]["username"] = $_SESSION["BSBX_UNAME"];
	$returnvals[0]["session"] = $_SESSION["BSBX_SES"];
	$returnvals[0]["authorized"] = $UserisAuthorized;

	$data = json_encode($returnvals);
	print $data;
	exit;
}

if ($tagId != "") {
// /			$MaincrudEngine->enableTracing(1);			
	$MaincrudEngine->changeDatabase($db_name);			
	$returnvals = $MaincrudEngine->read("bb_smartags", "WHERE id='" . $tagId . "' OR tagid='" . $tagId . "'", "*");    

	// Register timeclock action
	$returnvals[0]["authorized"] = $UserisAuthorized;		
	$returnvals[0]["req_tagid"] = "$tagId";
	if ($returnvals[0]["id"] != "") {

		if ($mode == "register") {

			$returnvals[0]["status"] = "is reg";
			$logData = "Record for tag $tagId exists.";
			$tagOwner = $returnvals[0]["owner"];
			$ownerRecords = $MaincrudEngine->read("users", "WHERE busibox_id='" . $tagOwner . "'", "*");   
			$tagUsername = urldecode($ownerRecords[0]["fullname"]);
			
			$prevRecord = $MaincrudEngine->read("timeclock_activities", "WHERE userid='" . $tagOwner . "' ORDER BY registered_time DESC", "*");   

			$prevRecordFlag = $prevRecord[0]["isstart"];
			$recordDate = date("Y-m-d H:i:s", time());
			

			$isstart = 0;
			$totaltime = 0;			
			if ($prevRecordFlag == 0) {
				$logData .= "<br>Clocking in...";			

				$clockAction = "ingeklokt";
				$returnvals[0]["clockin"] = "ingeklokt";	
				$returnvals[0]["clockout"] = " ";	
				
				$isstart = 1;
			} else {
				$logData .= "<br>Clocking out...";
				$clockAction = "uitgeklokt";
				$returnvals[0]["clockin"] = " ";	
				$returnvals[0]["clockout"] = "uitgeklokt";	
				$totaltime = strtotime($recordDate) - strtotime($prevRecord[0]["registered_time"]);
			}
			$returnvals[0]["totaltime"] = $totaltime;				
			$returnvals[0]["fullname"] = $tagUsername . " " . $clockAction;	
			$createRecord = $MaincrudEngine->create("timeclock_activities", Array("userid","registered_time","isstart"), Array("$tagOwner","$recordDate","$isstart"), "");    	
			
			
			
			$logData .= "<br>Creating timeclock activity for user $tagOwner on $recordDate";			
			$returnvals[0]["log"] = $logData;	
	$returnvals[0]["userid"] = $_SESSION["BSBX_UID"];
	$returnvals[0]["username"] = $_SESSION["BSBX_UNAME"];
		$returnvals[0]["session"] = $_SESSION["BSBX_SES"];
				if ($_SESSION["BSBX_SES"] != "") {

	}


		}
	} else {

		$returnvals[0]["tagid"] = "$tagId";
		$returnvals[0]["fullname"] = "Unknown user";	
		$returnvals[0]["log"] = "Unknow tag. Can not register activity.<br>Please provision card or contact you system administrator.";		
	}
	$data = json_encode($returnvals[0]);
} else {
	$MaincrudEngine->changeDatabase($db_name);			
	$returnvals = $MaincrudEngine->read("bb_smartags", "", "*");    
	$data = json_encode($returnvals);	
}




print $data;

?>