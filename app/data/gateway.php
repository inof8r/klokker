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

	$data = $result;	
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
}

$UserisAuthorized = 0;
$AdmincrudEngine->changeDatabase($main_db_name);
$isAuthorized = $AuthManagerService->authSession($_SESSION["BSBX_SES"]);



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
}

if ($mode == "getusers") {
	$MaincrudEngine->changeDatabase($db_name);					
	$getUsers = $MaincrudEngine->read("users", "","*");
	$getUsersDB = $MaincrudEngine->read("users", "","*");
	// decorate
	foreach($getUsersDB as $i) {	
		$finalItem = Array();
		$finalItem["id"] = $i["id"];
		$finalItem["fullname"] = urldecode($i["fullname"]);		
		$returnvalsData[] = $finalItem;
		
	}
	
	$data = json_encode($returnvalsData);	
	
}

if ($mode == "records") {
	$MaincrudEngine->changeDatabase($db_name);			
	$returnvalsDB = $MaincrudEngine->read("timeclock_activities", "", "*");    
	// decorate
	$stringTool = new StringTools();
	
	foreach($returnvalsDB as $dta) {
		$finalItem = Array();
		$finalItem["id"] = $i["id"];
		$finalItem["userid"] = $i["userid"];		
		$ownerParams = $MaincrudEngine->read("users", "WHERE id='" . $i["userid"] . "'", "*");    		
		$finalItem["ownername"] = urldecode($ownerParams[0]["fullname"]);
		$finalItem["registered_time"] = $i["registered_time"];				


	
	
	if ($dta["isstart"] == "0") {
		$registered_dbtime = date("Y-m-d H:i:s", strtotime($dta["registered_time"]));
		$user_data = $MaincrudEngine->read( "users", "WHERE id='" .  $dta["userid"]. "'", "id,fullname");
		$prev_record = $MaincrudEngine->read( "timeclock_activities", "WHERE userid='" .  $dta["userid"]. "' AND registered_time<'" . $registered_dbtime . "' AND isstart='1' ORDER BY registered_time DESC LIMIT 0,1", "*");
		if ($prev_record[0]["id"] == "") {
			if (date("d", strtotime($prev_record[0]["registered_time"])) != date("d", strtotime($dta["registered_time"]))) {
				$cur_project["registered_time"] = date("d-m-Y", strtotime($prev_record[0]["registered_time"]))  . " - " . date("d-m-Y", strtotime($dta["registered_time"])) ;			
			} else {
				$cur_project["registered_time"] =  date("d-m-Y", strtotime($dta["registered_time"])) ;
			}
			$cur_project["tijd"] = date("H:i:s", strtotime($prev_record[0]["registered_time"])) . " t/m " . date("H:i:s", strtotime($dta["registered_time"]));	
			$cur_project["ownername"] = $user_data[0]["fullname"] . " " . $dta["id"] . " prvid:" . $prev_record[0]["id"];			
			$cur_project["amount"] = $testDur;

			$cur_project["rowparams"]["norowclick"] = "1";

		
		} else {
			$hour_num = strtotime($dta["registered_time"]) - strtotime($prev_record[0]["registered_time"]);		
			$testDur = $stringTool->formatDuration($hour_num);
		
			if (date("d", strtotime($prev_record[0]["registered_time"])) != date("d", strtotime($dta["registered_time"]))) {
				$cur_project["registered_time"] = date("d-m-Y", strtotime($prev_record[0]["registered_time"]))  . " - " . date("d-m-Y", strtotime($dta["registered_time"])) ;			
			} else {
				$cur_project["registered_time"] =  date("d-m-Y", strtotime($dta["registered_time"])) ;
			}

			$cur_project["tijd"] = date("H:i:s", strtotime($prev_record[0]["registered_time"])) . " t/m " . date("H:i:s", strtotime($dta["registered_time"]));	
			$cur_project["ownername"] = urldecode($user_data[0]["fullname"]);			
			$cur_project["amount	"] = $testDur;

			$cur_project["rowparams"]["norowclick"] = "1";

	
			$hourtotal += $hour_num;	
		}	
		$returnvalsData[] = $cur_project;		
	}
	
	}

	
	$returnvals["authorized"] = $UserisAuthorized;	
	$returnvals["data"] = $returnvalsData;
	$data = json_encode($returnvals);
}


if ($mode == "gettagtypes") {
	$MaincrudEngine->changeDatabase($db_name);					
	$getTagTypes = $MaincrudEngine->read("bb_smartag_types", "","*");
	$data = json_encode($getTagTypes);	
}


if ($mode == "savetag") {
//	$MaincrudEngine->enableTracing(1);
	$returnvals[0]["authorized"] = $UserisAuthorized;		
	if($UserisAuthorized == 1) {
	
		$obid = $_POST["obid"];
		$tagid = $_POST["tagid"];	
		$owner = $_POST["owner"];			
		$obtype = $_POST["obtype"];			
		$note = $_POST["note"];					
		$fields = Array("tagid","owner","obtype","note");
		$values = Array("$tagid","$owner","$obtype","$note");		
		$MaincrudEngine->changeDatabase($db_name);					
		if ($obid == ""	) {
			$createTag = $MaincrudEngine->create("bb_smartags", $fields, $values, "");    	
		} else {
			$updateTag = $MaincrudEngine->update("bb_smartags", $fields, $values, "WHERE id='$obid'");    	
		}
	
		$returnvals[0]["req_tagid"] = "$tagid";
		$returnvals[0]["log"] = "Tag $tagid saved";
		$returnvals[0]["result"] = "success";			
	
	} else {
		$returnvals[0]["result"] = "failed";			
		$returnvals[0]["log"] = "User not authorized";
	}
	$data = json_encode($returnvals);
	print $data;
	exit;

}


if ($mode == "gettag") {
if ($tagId != "") {
// /			$MaincrudEngine->enableTracing(1);			
	$MaincrudEngine->changeDatabase($db_name);			
	$returnvals = $MaincrudEngine->read("bb_smartags", "WHERE id='" . $tagId . "' OR tagid='" . $tagId . "' AND obtype='5'", "*");    

	// Register timeclock action
	$returnvals[0]["authorized"] = $UserisAuthorized;		
	$returnvals[0]["req_tagid"] = "$tagId";
	if ($returnvals[0]["id"] != "") {

		if ($mode == "register") {

			$returnvals[0]["status"] = "is reg";
			$logData = "Record for tag $tagId exists.";
			$tagOwner = $returnvals[0]["owner"];
			$ownerRecords = $MaincrudEngine->read("users", "WHERE id='" . $tagOwner . "'", "*");   
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
				$returnvals[0]["cstatus"] = "in";					
				$isstart = 1;
			} else {
				$logData .= "<br>Clocking out...";
				$clockAction = "uitgeklokt";
				$returnvals[0]["clockin"] = " ";	
				$returnvals[0]["clockout"] = "uitgeklokt";	
				$returnvals[0]["cstatus"] = "out";									
				$totaltime = strtotime($recordDate) - strtotime($prevRecord[0]["registered_time"]);
			}
			$returnvals[0]["totaltime"] = $totaltime;				
			$returnvals[0]["fullname"] = $tagUsername . " " . $clockAction;	
			$createRecord = $MaincrudEngine->create("timeclock_activities", Array("userid","registered_time","isstart"), Array("$tagOwner","$recordDate","$isstart"), "");    	
			
			
			
			$logData .= "<br>Creating timeclock activity for user $tagOwner on $recordDate";			
		

		}
			$returnvals[0]["log"] = $logData;	
			$returnvals[0]["userid"] = $_SESSION["BSBX_UID"];
			$returnvals[0]["username"] = $_SESSION["BSBX_UNAME"];
			$returnvals[0]["session"] = $_SESSION["BSBX_SES"];



	} else {

		$returnvals[0]["tagid"] = "$tagId";
		$returnvals[0]["cstatus"] = "none";
		$returnvals[0]["fullname"] = "Unknown user";	
		$returnvals[0]["log"] = "Tag not registered yet";		
	}
	$returnvals[0]["authorized"] = $UserisAuthorized;	
	$data = json_encode($returnvals[0]);
} else {
	$MaincrudEngine->changeDatabase($db_name);			
	$returnvalsDB = $MaincrudEngine->read("bb_smartags", "", "*");    
	// decorate
	foreach($returnvalsDB as $i) {
		$finalItem = Array();
		$finalItem["id"] = $i["id"];
		$finalItem["tagid"] = $i["tagid"];		
		$finalItem["owner"] = $i["owner"];		
		$ownerParams = $MaincrudEngine->read("users", "WHERE id='" . $i["owner"] . "'", "*");    		
		$finalItem["ownername"] = urldecode($ownerParams[0]["fullname"]);
		$obtypeParams = $MaincrudEngine->read("bb_smartag_types", "WHERE id='" . $i["obtype"] . "'", "*");    		
		$finalItem["obtype"] = $i["obtype"];				
		$finalItem["obtypename"] = $obtypeParams[0]["name"];				
		$finalItem["note"] = $i["note"];						
		$returnvalsData[] = $finalItem;
	}
	$returnvals	["authorized"] = $UserisAuthorized;	
	$returnvals["data"] = $returnvalsData;
	$data = json_encode($returnvals);	
}
}




print $data;

?>