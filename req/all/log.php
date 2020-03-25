<?php
/**
 * This Logging API is used for the Hawg-Ops website at https://hawg-ops.com
 *
 * It is written and maintained by @chris-m92, "Porkins"
 * It is owned by AirmenCoders and the U.S. Air Force
 * 
 * https://github.com/airmencoders/hawgops.git
 * 
 * LICENSE: MIT
 * Copyright (c) 2020 Airmen Coders
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial 
 * portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

//==============================================================
// Log File Constants
//==============================================================
$basePath = "/opt/bitnami/apache2/htdocs";
$logPath = $basePath."/logs";
$filename = date("Y-m-d").".txt";

//==============================================================
// Check for log file, keep size under control, and open log file
//==============================================================
$log = $logPath.$filename;

if (file_exists($log) && (filesize($log) > 1500000)) {
	rename ($log, $log.".old");
}

// Mode: a - Write only, pointer at end of file
// Mode: b - Binary flag, no Windows translation of \n
$logFile = fopen($log, "ab");

//==============================================================
// Open $logFile or display an error if there was one
//==============================================================
if(!$logFile) {
	echo "Unable to open log file: [".$log."]";
	exit;
}

//==============================================================
// Functions
//==============================================================

/**
 * createLog
 * 
 * Creates a JSON encoded log message to be appended to the $logFile
 * 
 * @param String $level Bootstrap contextual class.
 * @param String $code Status Code.
 * @param String $caller Script creating the log entry.
 * @param String $function Function name within Script.
 * @param String $action Short description of the log entry.
 * @param String $details Further detailed information of the log entry.
 * 
 * @return Null 
 */
function createLog($level="info", $code="-", $caller="-", $function="-", $activity="NULL Log", $details="-") {
	global $logFile;

	$date = date("Y-m-d h:i:s");

	if(!isset($_SESSION["id"]) || $_SESSION["id"] == "") {
		$user = "Guest";
	} else {
		$user = getUserEmail("id", $_SESSION["id"]);
	}

	$array = array(
		"level"		=> $level,
		"datetime"	=> $date,
		"ip"		=> $_SERVER["REMOTE_ADDR"],
		"user"		=> $user,
		"code"		=> $code,
		"caller"	=> $caller,
		"function"	=> $function,
		"activity"	=> $activity,
		"details"	=> $details
	);

	$json = json_encode($array);
	fwrite($logFile, $json.",");
}

/**
 * closeLogs
 * 
 * Flush $logFile buffers and close the log file.
 * 
 * @param null
 * 
 * @return null
 */
function closeLogs() {
	global $logFile;
	fclose($logFile);
	exit;
}
?>