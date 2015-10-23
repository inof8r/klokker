<?php
session_start();
/**
* StringTools
*
* http://www.inof8.nl
*
* StringTools service
*
* IMPORTANT NOTE
* there is no warranty, implied or otherwise with this software.
* 
*
* @author		Joey Delemarre <info@inof8.nl>
* @version 	001
* @package	StringTools
*/
class StringTools {
	var $curString="";

	function StringTools (){
		// nothing to be done
	}
	function formatDuration($seconds){
		$hours = floor($seconds/3600);
		$minutes = 0;

		$remaining = $seconds % 3600;
		$minutes = $remaining/60;		

		if ($remaining > 0) {
			$minutes = floor($remaining/60);
			$remainingSecs = $remaining % 60;			
			$seconds = $remainingSecs;
		}
		$hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
		$minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);		
		$seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);				
		$newString = "$hours:$minutes:$seconds";
		return $newString;
	}
	
} // end of class

?>