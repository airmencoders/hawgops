<?php
/**
 * This API is used for the Hawg-Ops website at https://hawg-ops.com
 * It is written and maintained by @chris-m92, "Porkins"
 * It is owned by AirmenCoders and the U.S. Air Force
 * 
 * LICENSE: MIT
 */

//==============================================================
// Start the PHP Session
//==============================================================
session_start();

//==============================================================
// Log File Constants
//==============================================================
$apiVersion = 2;
$basePath = "/opt/bitnami/apache2/htdocs";
$logPath = $basePath."/logs";
$filename = date("Y-m-d").".txt";

// Check for log file, keep size under control, and open log file
$log = $logPath.$filename;

if (file_exists($log) && (filesize($log) > 1500000)) {
	rename ($log, $log.".old");
}

// Mode: a - Write only, pointer at end of file
// Mode: b - Binary flag, no Windows translation of \n
$logFile = fopen($log, "ab");

// Open $logFile or display an error if there was one
if(!$logFile) {
	echo "Unable to open log file: [".$log."]";
	exit;
}

//==============================================================
// Functions
//==============================================================

/**
 * changePassword
 * 
 * @param
 * 
 * @return
 */
function changePassword() {

}

/**
 * closeLogs
 * 
 * @param
 * 
 * @return
 */
function closeLogs() {

}

/**
 * createAccount
 * 
 * @param
 * 
 * @return
 */
function createAccount() {

}



?>