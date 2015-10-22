<?php
require 'vendor/autoload.php';

define('APPLICATION_NAME', 'Google API Service');
define('CREDENTIALS_PATH', 'drive-api-quickstart.json');
define('CLIENT_SECRET_PATH', 'client_secret.json');

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */


class Busibox_Google_Client
{
	public $credentialsPath;
	public $clientSecretPath;	
	public $client;
	public $service;
	public $curAccessToken;	
	public function __construct($config = null)
	{
  		if ($config) {

  			$this->credentialsPath = $config["cfg_dir"] . 'drive-api-quickstart.json';
  			$this->clientSecretPath = $config["cfg_dir"] . 'client_secret.json';

  		}
	}
	
	public function getClient()
	{
		print $clientSecretPath;
	  $credentialsPath = $this->credentialsPath;

	  $client = new Google_Client();
	  $client->setApplicationName(APPLICATION_NAME);
	  //$client->setScopes(SCOPES);
	  $client->setAuthConfigFile($this->clientSecretPath);
	 // $client->setAccessType('offline');

	  // Load previously authorized credentials from a file.
	

	  if (file_exists($credentialsPath)) {
		$accessToken = file_get_contents($credentialsPath);
	  } else {
		// Request authorization from the user.
		$authUrl = $client->createAuthUrl();
		printf("Open the following link in your browser:\n%s\n", $authUrl);
		print 'Enter verification code: ';
		$authCode = trim(fgets(STDIN));

		// Exchange authorization code for an access token.
		$accessToken = $client->authenticate($authCode);

		// Store the credentials to disk.
		if(!file_exists(dirname($credentialsPath))) {
		  mkdir(dirname($credentialsPath), 0700, true);
		}
		file_put_contents($credentialsPath, $accessToken);
		printf("Credentials saved to %s\n", $credentialsPath);
	  }
	  $client->setAccessToken($accessToken);

	  // Refresh the token if it's expired.
	  if ($client->isAccessTokenExpired()) {
		$client->refreshToken($client->getRefreshToken());
		file_put_contents($credentialsPath, $client->getAccessToken());
	  }
	  $this->client = $client;
	  return $this->client;
	}
	
	public function startService($type = null) 
	{
	
		$client = $this->getClient();
		
		//$client->refreshToken($this->curAccessToken);
		$this->service = null;
		if ($type == "calendar") {
			$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
			$this->service = new Google_Service_Calendar($client);
		} else {
			$client->addScope(Google_Service_Drive::DRIVE);
			$this->service = new Google_Service_Drive($client);
		}
	}
	
	public function reAuthenticate() 
	{	

				$authUrl = $this->client->createAuthUrl();
		printf("Open the following link in your browser:\n%s\n", $authUrl);
		print 'Enter verification code: ';
		$authCode = trim(fgets(STDIN));

	}	
	
	public function getFiles() 
	{
	
		// Print the names and IDs for up to 10 files.
		$optParams = array(
		  'maxResults' => 25,
		);
		$results = $this->service->files->listFiles($optParams);

		if (count($results->getItems()) == 0) {
		  return "No files found.\n";
		} else {
			return $results->getItems();
		}
	
	}
	
	public function searchFiles($str) 
	{

		$searchQuery = "fullText contains '" . $str . "'";
		$optParams = array(
		  'maxResults' => 2500,
		  'q' => $searchQuery,
		  'orderBy'=> 'title'		  
		);
		$children = $this->service->files->listFiles($optParams);


		if (count($children->getItems()) == 0) {
		  return false;
		} else {
			return $children->getItems();
		}
	
	}
	
	public function searchFolders($str) 
	{

		$searchQuery = "title contains '" . $str . "'";
		$searchQuery .= " and mimeType='application/vnd.google-apps.folder'";
		$optParams = array(
		  'maxResults' => 2500,
		#  'orderBy'=> 'folder,title',
		  'q' => $searchQuery

		);
		$children = $this->service->files->listFiles($optParams);


		if (count($children->getItems()) == 0) {
		  return false;
		} else {
			return $children->getItems();
		}
	
	}
	
	
	public function addFile($opt) 
	{
		$file = new Google_Service_Drive_DriveFile();
		$file->setTitle($opt["filename"]);
		$file->setDescription($opt["description"]);
		$file->setMimeType('image/jpeg');

	  // Set the parent folder.
	  $parentId = $opt["parent"];
	  if ($parentId != "") {
    	$parent = new Google_Service_Drive_ParentReference();
	    $parent->setId($parentId);
    	$file->setParents(array($parent));
	  }

		$data = file_get_contents($opt["src"]);

		$createdFile = $this->service->files->insert($file, array(
			  'data' => $data,
			  'mimeType' => 'image/jpeg',
			  'uploadType' => 'multipart'
			));
		return $createdFile;
	}	
	
	public function addFolder($opt) 
	{

		$file = new Google_Service_Drive_DriveFile();
		$file->setTitle($opt["filename"]);
		$file->setDescription($opt["description"]);
		$file->setMimeType('application/vnd.google-apps.folder');
		
	  // Set the parent folder.
	  $parentId = $opt["parent"];
	  if ($parentId != "") {
    	$parent = new Google_Service_Drive_ParentReference();
	    $parent->setId($parentId);
    	$file->setParents(array($parent));
	  }
		
		$createdFolder = $this->service->files->insert($file, array(
			  'mimeType' => 'application/vnd.google-apps.folder'
			));
		return $createdFolder;
	}	
	
	public function copyFolderRecursive($parent,$folder) 
	{

		if (is_dir($folder)) {
			if ($dh = opendir($folder)) {
				while (($file = readdir($dh)) !== false) {

					$abspath = utf8_encode($folder . $file);
					if (substr($file,0,1) != ".") {				
						if (is_dir($abspath)) {		
							$folderOptions = Array(
								'filename' => $file,
								'description' => 'Uploaded via Busibox',
								'parent' => $parent	

							);
		
							$newparent = $this->addFolder($folderOptions);
							$this->copyFolderRecursive($newparent->getId(),$abspath . "/");
						} else {
							$fileOptions = Array(
								'src' => $abspath,
								'filename' => $file,
								'description' => 'Uploaded via Busibox',
								'parent' => $parent
							);							
							$newfile = $this->addFile($fileOptions);							
						}
					}
				}
				closedir($dh);
			}
		}	
		
		// $createdFolder = $this->service->files->insert($file, array(
// 			  'mimeType' => 'application/vnd.google-apps.folder'
// 			));
		return $createdFolder;
	}	
	

	public function getFileParent($id) 
	{
		
		$file = $this->service->files->get($id);
		$parents  = $file->getParents();
		if (count($parents) > 0) {
		foreach($parents as $p) {
			$plist[] = $p->getId();
		}
		try {
			$newpid = $parents[0]->getId();
		} catch (Exception $e) {
			$newpid = null;			
		}
		}
		return $newpid;			

	}	

	public function folderExists($name, $parent) 
	{
		
		$searchQuery = "title contains '" . $name . "'";
		$searchQuery .= " and '" . $parent . "' in parents ";		

		$optParams = array(
		  'maxResults' => 2500,
		#  'orderBy'=> 'folder,title',
		  'q' => $searchQuery

		);
		$children = $this->service->files->listFiles($optParams);


		if (count($children->getItems()) == 0) {
		  return $searchQuery;
		} else {
			return $children->getItems();
		}		

	}	

	public function updateFilename($id,$newname) 
	{
		
		$file = $this->service->files->get($id);
		$file->setTitle($newname);
		$updateFile = $this->service->files->update($id, $file, Array());
		return $updateFile->getId();
	}	
	
	public function moveFile($fileId, $newParentId) {
		
		$curParent = $this->getFileParent($fileId);		
		if ($curParent != $newParentId) {
		try {
			$file = new Google_Service_Drive_DriveFile();

			$parent = new Google_Service_Drive_ParentReference();
			$parent->setId($newParentId);

			$file->setParents(array($parent));

			$updatedFile = $this->service->files->patch($fileId, $file);

			return $updatedFile->getId();
		} catch (Exception $e) {
			return "An error occurred: " . $e->getMessage();
		}
		}
	}	

	public function getCalendarItems() {

	
		// Print the next 10 events on the user's calendar.
		$calendarId = 'primary';
		$optParams = array(
		  'maxResults' => 10,
		  'orderBy' => 'startTime',
		  'singleEvents' => TRUE,
		  'timeMin' => date('c'),
		);
		$results = $this->service->events->listEvents($calendarId, $optParams);

		if (count($results->getItems()) == 0) {
		  print "No upcoming events found.\n";
		} else {
		  print "Upcoming events:\n";
		  foreach ($results->getItems() as $event) {
			$start = $event->start->dateTime;
			if (empty($start)) {
			  $start = $event->start->date;
			}
			printf("%s (%s)\n", $event->getSummary(), $start);
		  }
		}

	}

}






?>
