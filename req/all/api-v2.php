<?php
/**
 * This API is used for the Hawg-Ops website at https://hawg-ops.com
 *
 * It is written and maintained by @chris-m92, "Porkins"
 * It is owned by AirmenCoders and the U.S. Air Force
 * 
 * https://github.com/airmencoders/hawgops.git
 * https://github.com/airmencoders/hawgops/req/all/api-v2.php
 * 
 * LICENSE: MIT
 * Copyright (c) 2020 Airmen Coders
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
 * SOFTWARE.
 * 
 * @author 		Porkins
 * @copyright 	2020 Airmen Coders
 * @license 	MIT
 * @version 	2.0.0	Added SQL Execution checks.
 * 						Added various modes to functions to reduce total number
 * 						of functions.
 */

//==============================================================
// Start the PHP Session
//==============================================================
session_start();

//==============================================================
// Log File Constants
//==============================================================
$logPath = "/opt/bitnami/apache2/htdocs/logs/";
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
	header("Location: /db-error");
	closeLogs();
}

//==============================================================
// Functions
//==============================================================

/**
 * changePassword
 * 
 * Changes a user account password. Used as a middle man to enable / verify 
 * account recovery tokens
 * 
 * @param 	string 	$id 		User Account Identifier
 * @param 	string 	$password 	New password
 * @param	bool	$recovery	Tells the function if this is in recovery mode
 * @param	string	$email		User Email Address
 * @param	string	$token		Recover token
 * @return	int					Status code
 * @since 			1.0.0
 * @since 			2.0.0		Added mode functionality
 */
function changePassword($id, $password, $recovery = false, $email = null, $token = null) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserPassword;
	global $colUserId;

	// Status Code 
	global $E_EMAIL_NOT_RCVD;
	global $E_MYSQL;
	global $E_PSWD_NOT_RCVD;
	global $E_TOKEN_NOT_RCVD;
	global $E_UNAUTHORIZED;
	global $E_USER_ID_NOT_RCVD;
	global $S_ACNT_ENABLED;
	global $S_PSWD_CHANGED;

	// Ensure that variables were set
	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	if(!isset($password) || $password == "") {
		createLog("warning", $E_PSWD_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Password");
		return $E_PSWD_NOT_RCVD;
	}

	// If not recovering an account, only the user is allowed to change a password
	if(!$recovery && $id != $_SESSION["id"]) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to change password", "Email: [".getUserEmail("id", $_SESSION["id"])."]");
		return $E_UNAUTHORIZED;
	}

	// If Recovering an account, enable the account and delete the ticket
	// Do this first, becuase if there are issues with the token status, then
	// we want to figure that out before changing the password.
	if($recovery) {
		if(!isset($email) || $email == "") {
			createLog("warning", $E_EMAIL_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Email address");
			return $E_EMAIL_NOT_RCVD;
		}

		if(!isset($token) || $token == "") {
			createLog("warning", $E_TOKEN_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Recovery Token");
			return $E_TOKEN_NOT_RCVD;
		}

		// Enable the account (This also removes the token from the database)
		$enableStatus = enableAccount($id, true, $email, $token);
		if($enableStatus != $S_ACNT_ENABLED) {
			createLog("warning", $enableStatus, basename(__FILE__), __FUNCTION__, "Failed to enable account");
			return $enableStatus;
		}
	}
	
	// Change the password
	$password = password_hash($password, PASSWORD_DEFAULT);

	$query = "UPDATE $tblUsers SET $colUserPassword = ? WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ss", $password, $id);
		$statement->execute();
		$statement->close();
		createLog("success", $S_PSWD_CHANGED, basename(__FILE__), __FUNCTION__, "Password changed", "Email: [$email]");
		return $S_PSWD_CHANGED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * closeLogs
 * 
 * Flush $logFile buffers and close the log file.
 * 
 * @param 	void
 * @return 	void
 * @since 			1.0.0
 */
function closeLogs() {
	// Variables
	global $logFile;

	fclose($logFile);
	exit;
}

/**
 * createAccount
 * 
 * Creates a user account for use with Hawg Ops. Only allows one user account
 * per email address. Assumes that security criteria is already enforced.
 * 
 * @param	string	$fname		User's first name
 * @param	string	$lname		User's last name
 * @param	string	$email		User's Email address
 * @param	string	$password	User's password * 
 * @return	int					Status code
 * @since 			1.0.0
 * @since			2.0.0		Enforce Camel Case for names
 */
function createAccount($fname, $lname, $email, $password) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserEmail;
	global $colUserFname;
	global $colUserId;
	global $colUserJoined;
	global $colUserLname;
	global $colUserPassword;	

	// Status codes
	global $E_ACNT_EXISTS;
	global $E_EMAIL_NOT_RCVD;
	global $E_FNAME_NOT_RCVD;
	global $E_LNAME_NOT_RCVD;
	global $E_MYSQL;
	global $E_PSWD_NOT_RCVD;
	global $S_ACNT_CREATED;

	// Ensure that variables are set
	if(!isset($fname) || $fname == "") {
		createLog("warning", $E_FNAME_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "First name");
		return $E_FNAME_NOT_RCVD;
	}

	if(!isset($lname) || $lname == "") {
		createLog("warning", $E_LNAME_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Last name");
		return $E_LNAME_NOT_RCVD;
	}

	if(!isset($email) || $email == "") {
		createLog("warning", $E_EMAIL_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Email address");
		return $E_EMAIL_NOT_RCVD;
	}

	if(!isset($password) || $password == "") {
		createLog("warning", $E_PSWD_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Password");
		return $E_PSWD_NOT_RCVD;
	}

	// Enforce word case
	$email = strtolower($email);
	$fname = ucwords($fname);
	$lname = ucwords($lname);

	// Confirm that no account already exists for the provided email address
	$dbUserId = null;
	$query = "SELECT $colUserId FROM $tblUsers WHERE $colUserEmail = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($dbUserId);
		
		if($statement->fetch() != null) {
			createLog("warning", $E_ACNT_EXISTS, basename(__FILE__), __FUNCTION__, "Account already exists", "Email: [$email]");
			$statement->close();
			return $E_ACNT_EXISTS;
		}
		$statement->close();
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	// Create data
	$joined = date("Y-m-d H:i:s");
	$id = createUUID();

	// Hash the password
	$password = password_hash($password, PASSWORD_DEFAULT);

	// Create the Account
	$query = "INSERT INTO $tblUsers ($colUserId, $colUserFname, $colUserLname, $colUserEmail, $colUserPassword, $colUserJoined) VALUES (?, ?, ?, ?, ?, ?)";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ssssss", $id, $fname, $lname, $email, $password, $joined);
		$statement->execute();
		$statement->close();
		createLog("success", $S_ACNT_CREATED, basename(__FILE__), __FUNCTION__, "Account Created", "Email: [$email]");
		return $S_ACNT_CREATED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * createLog
 * 
 * Creates a JSON encoded log message to be appended to the $logFile
 * 
 * @param 	string 	$level 		Bootstrap contextual class.
 * @param 	string 	$code 		Status Code.
 * @param 	string 	$caller 	Script creating the log entry.
 * @param 	string 	$function 	Function name within Script.
 * @param 	string 	$action 	Short description of the log entry.
 * @param 	string 	$details 	Further detailed information of the log entry.
 * @return 	void
 * @since 			1.0.0
 * @since 			2.0.0 		Added default values. Removed unused location 
 * 								data.
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
 * createToken
 * 
 * Generates a random string, then hashes it with SHA-256 to use as a token for account
 * recovery purposes.
 * Replaces createKey in API v1.
 * 
 * @param 	void
 * @return 	string 				SHA-256 Hashed token for use as account recovery tokens
 * @since 			1.0.0
 * @since			2.0.0		Renamed to createToken
 */
function createToken() {
	// Character seed
	$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()";

	// Seed length
	$length = 25;

	// Initialize variables
	$charactersLength = strlen($characters);
	$string = "";

	// Create seed string
	for($i = 0; $i < $length; $i++) {
		$string .= $characters[rand(0, $charactersLength - 1)];
	}

	// Prepend the time to further randomize hash
	$string = date("U").$string;

	return hash("sha256", $string);
}

/**
 * createUUID
 * 
 * Creates a UUID version 4 for use as database Identifiers
 * 
 * @param 	void
 * @return 	string 			Formatted UUID for use with database entries
 * @since 			2.0.0
 * @see https://www.php.net/manual/en/function.uniqid.php (Andrew Moore Comment)
 * @see https://tools.ietf.org/html/rfc4122
 */
function createUUID() {
	return sprintf('%04x%04x-%04x-%04x-%04x%04x%04x',
						// 32 bits for "time_low"
						mt_rand(0, 0xffff), mt_rand(0, 0xffff),

						// 16 bits for "time_mid"
						mt_rand(0,0xffff),

						// 16 bits for "time_hi_and_version"
						// Four most significant bits holds version number 4
						mt_rand(0, 0x0fff) | 0x4000,

						// 16 bits, 8 biuts for "clk_seq_hi_res"
						// 8 bits for "clk_seq_low"
						// Two most significant bits holds zero and one for variant DCE1.1
						mt_rand(0, 0x3fff) | 0x8000,

						// 48 bits for "node"
						mt_rand(0,0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}

/**
 * deleteAccount
 * 
 * Deletes a User Account and all associated Scenarios
 */
function deleteAccount($id, $security = false) {

}

/**
 * deleteRecoveryToken
 * 
 * Deletes a user's recovery token from the database
 * 
 * @param	string	$criteria	Criteria for database query
 * @return	int					Status code
 * @since			2.0.0
 */
function deleteRecoveryToken($criteria, $mode = "email") {
	// Database variables
	global $db;
	global $tblRecovery;
	global $colRecoveryEmail;
	global $colRecoveryId;

	// Status Codes
	global $E_CRITERIA_NOT_RCVD;
	global $E_MODE_INVALID;
	global $E_MYSQL;
	global $S_TOKEN_DELETED;

	if(!isset($criteria) || $criteria == "") {
		createLog("warning", $E_CRITERIA_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Database Criteria");
		return $E_CRITERIA_NOT_RCVD;
	}

	if($mode == "email") {
		$query = "DELETE FROM $tblRecovery WHERE $colRecoveryEmail = ?";
	} else if($mode == "id") {
		$query = "DELETE FROM $tblRecovery WHERE $colRecoveryId = ?";
	} else {
		createLog("warning", $E_MODE_INVALID, basename(__FILE__), __FUNCTION__, "Invalid mode received", "Mode: [$mode]");
		return $E_MODE_INVALID;
	}

	// Delete the recovery token
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $criteria);
		$statement->execute();
		$statement->close();
		createLog("success", $S_TOKEN_DELETED, basename(__FILE__), __FUNCTION__, "Token Deleted", "$mode: [$criteria]");
		return $S_TOKEN_DELETED;
	} else {
		createLog("warning", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * deleteScenario
 * 
 * Deletes a CAS Scenario from the database
 * 
 * @param 	string 	$id 	Scenario Identifier
 * @return 	int 			Status code
 * @since 			1.0.0
 */
function deleteScenario($id) {
	// Database variables
	global $db;
	global $tblScenarios;
	global $colScenarioId;
	global $colScenarioUser;

	// Status codes
	global $E_MYSQL;
	global $E_SCEN_DOESNT_EXIST;
	global $E_SCEN_ID_NOT_RCVD;
	global $E_UNAUTHORIZED;
	global $S_SCEN_DELETED;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_SCEN_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Scenario ID");
		return $E_SCEN_ID_NOT_RCVD;
	}

	// Get the scenario owner from the database
	$dbScenarioUser = null;
	$query = "SELECT $colScenarioUser FROM $tblScenarios WHERE $colScenarioId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($dbScenarioUser);
		if($statement->fetch() == null) {
			createLog("warning", $E_SCEN_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Scenario does not exist", "ID: [$id]");
			$statement->close();
			return $E_SCEN_DOESNT_EXIST;
		}
		$statement->close();
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	// Ensure that the user is either the owner or an administrator
	if(!isAdmin() && $dbScenarioUser != $_SESSION["id"]) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to delete scenario [$id]", "Email: ".getUserEmail("id", $_SESSION["id"]));
		return $E_UNAUTHORIZED;
	}

	// Delete the scenario
	$query = "DELETE FROM $tblScenarios WHERE $colScenarioId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->close();
		createLog("success", $S_SCEN_DELETED, basename(__FILE__), __FUNCTION__, "Scenario Deleted", "ID: [$id]");
		return $S_SCEN_DELETED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * disableAccount
 * 
 * Disables a user's account. Requires them to recover account and reset
 * their password. Used for inactive accounts and accounts with too many
 * failed login attempts.
 * 
 * @param 	string 	$id 		User Account Identifier
 * @param	bool	$security	Whether or not this is an action due to security
 * @return 	int 				Status code
 * @since 			1.0.0
 * @since			2.0.0		Added Security flag
 */
function disableAccount($id, $security = false) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserDisabled;
	global $colUserId;

	// Status codes
	global $E_MYSQL;
	global $E_UNAUTHORIZED;
	global $E_USER_ID_NOT_RCVD;
	global $S_ACNT_DISABLED;

	// Ensure that the user is authorized to disable an account
	// Security flag is so that CRON jobs can perform this function also
	if(!$security && !isAdmin()) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to disable accounts", getUserEmail("id", $_SESSION["id"]));
		return $E_UNAUTHORIZED;
	}

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	$query = "UPDATE $tblUsers SET $colUserDisabled = 1 WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->close();
		createLog("success", $S_ACNT_DISABLED, basename(__FILE__), __FUNCTION__, "Account Disabled", "Email: [".getUserEmail("id", $id)."]");
		return $S_ACNT_DISABLED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * enableAccount
 * 
 * Enables a user's account.
 * 
 * @param 	string 	$id 		User Account Identifier
 * @param	bool	$recovery	Whether or not to operate in an account recovery mode
 * @param	string	$email		User Account Email
 * @param	string	$token		Account recovery token
 * @return 	int 				Status code
 * @since 			1.0.0
 * @since			2.0.0		Added recovery mode
 */
function enableAccount($id, $recovery = false, $email = null, $token = null) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserDisabled;
	global $colUserId;

	// Status codes
	global $E_EMAIL_NOT_RCVD;
	global $E_MYSQL;
	global $E_TOKEN_NOT_RCVD;
	global $E_UNAUTHORIZED;
	global $E_USER_ID_NOT_RCVD;
	global $S_ACNT_ENABLED;
	global $S_TOKEN_DELETED;
	global $S_TOKEN_VALID;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	// If not recovering an account, only administrators can enable an account
	if(!isAdmin() && !$recovery) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to enable accounts", "Email: [".getUserEmail("id", $_SESSION["id"]));
		return $E_UNAUTHORIZED;
	}

	if($recovery) {
		if(!isset($email) || $email == "") {
			createLog("warning", $E_EMAIL_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Email Address");
			return $E_EMAIL_NOT_RCVD;
		}

		if(!isset($token) || $token == "") {
			createLog("warning", $E_TOKEN_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Account Recovery Token");
			return $E_TOKEN_NOT_RCVD;
		}

		$tokenStatus = validateRecoveryToken($email, $token);
		if($tokenStatus != $S_TOKEN_VALID) {
			createLog("warning", $tokenStatus, basename(__FILE__), __FUNCTION__, "Failed to validate Recovery Token");
			return $tokenStatus;
		}
	}

	// Enable the account
	$query = "UPDATE $tblUsers SET $colUserDisabled = 0 WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->close();
		createLog("success", $S_ACNT_ENABLED, basename(__FILE__), __FUNCTION__, "Account Enabled", "Email: [".getUserEmail("id", $id)."]");
		// Delete the token from the database
		deleteRecoveryToken($email);
		return $S_ACNT_ENABLED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * getAllUsers
 * 
 * ADMIN FUNCTION: Retrieves a list of all User Accounts and their attributes.
 * 
 * @param 	void
 * @return 	array 			Array of all users and their attributes
 * @since 			1.0.0
 */
function getAllUsers() {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserId;
	global $colUserEmail;
	global $colUserFname;
	global $colUserLname;
	global $colUserDisabled;
	global $colUserAdmin;
	global $colUserJoined;
	global $colUserLastLogin;

	// Status Codes
	global $E_MYSQL;
	global $E_UNAUTHORIZED;

	if(!isAdmin()) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to view all users", "Email: [".getUserEmail("id", $_SESSION["id"])."]");
		return $E_UNAUTHORIZED;
	}

	$dbId = null;
	$dbEmail = null;
	$dbFname = null;
	$dbLname = null;
	$dbDisabled = null;
	$dbAdmin = null;
	$dbJoined = null;
	$dbLastLogin = null;

	$query = "SELECT $colUserId, $colUserEmail, $colUserFname, $colUserLname, $colUserDisabled, $colUserAdmin, $colUserJoined, $colUserLastLogin FROM $tblUsers";
	if($statement = $db->prepare($query)) {
		$statement->execute();
		$statement->bind_result($dbId, $dbEmail, $dbFname, $dbLname, $dbDisabled, $dbAdmin, $dbJoined, $dbLastLogin);

		$responseArray = array();
		while($statement->fetch()) {
			$tempArray = array(
							"id" 		=> $dbId,
							"email"		=> $dbEmail,
							"fname"		=> $dbFname,
							"lname"		=> $dbLname,
							"disabled"	=> $dbDisabled,
							"admin"		=> $dbAdmin,
							"joined"	=> $dbJoined,
							"lastLogin"	=> $dbLastLogin
			);
			array_push($responseArray, $tempArray);
		}

		$statement->close();
		return $responseArray;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * getIPLog
 * 
 * ADMIN FUNCTION: Retrieves a list of a User's IP Addresses used to access the website.
 * Replaces getIPLogByUser in API v1
 * 
 * @param 	string 	$id 	User Account Identifier
 * @return 	array 			Array of user's IP addresses and the first date it was used
 * @since 			1.0.0
 * @since			2.0.0	Renamed to getIPLog
 * @todo 			Change description to read "most recent date it was used" once update login function.
 */
function getIPLog($id) {
	// Database variables
	global $db;
	global $tblIplog;
	global $colIplogDate;
	global $colIplogIp;
	global $colIplogUser;

	// Status codes
	global $E_MYSQL;
	global $E_UNAUTHORIZED;
	global $E_USER_ID_NOT_RCVD;

	if(!isAdmin()) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to view IP Logs", "Email: [".getUserEmail("id", $_SESSION["id"])."]");
		return $E_UNAUTHORIZED;
	}

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	$dbIp = null;
	$dbDate = null;
	$query = "SELECT $colIplogIp, $colIplogDate FROM $tblIplog WHERE $colIplogUser = ? ORDER BY $colIplogDate";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($dbIp, $dbDate);

		$ipArray = array();
		while($statement->fetch()) {
			$tempArray = array(
							"ip"	=> $dbIp,
							"date"	=> $dbDate
			);
			array_push($ipArray, $tempArray);
		}

		$statement->close();
		return $ipArray;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * getScenarioCount
 * 
 * Retrieves the number of CAS Scenarios a user has created.
 * Replaces getNumberOfScenariosByUser in API v1
 * 
 * @param 	string 	$id 	User Identifier
 * @return 	int 			Count of user's CAS Scenarios or error code
 * @since 			1.0.0
 * @since 			2.0.0 	Renamed to getScenarioCount
 */
function getScenarioCount($id) {
	// Database variables
	global $db;
	global $tblScenarios;
	global $colScenarioId;
	global $colScenarioUser;

	// Status codes
	global $E_MYSQL;
	global $E_UNAUTHORIZED;

	if(!isAdmin()) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to view scenario count", getUserEmail("id", $_SESSION["id"]));
		return $E_UNAUTHORIZED;
	}

	$dbCount = null;
	$query = "SELECT COUNT($colScenarioId) AS NUM FROM $tblScenarios WHERE $colScenarioUser = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($dbCount);
		$statement->fetch();
		$statement->close();
		return $dbCount;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * getScenarioData
 * 
 * Retrieves Scenario Data from the database to be loaded into a Leaflet.js map.
 * Replaces getScenario in API v1
 * 
 * @param 	string 		$id 	Scenario Identifier
 * @return 	string|int 			JSON Encoded data or error code
 * @since 				1.0.0
 * @since 				2.0.0 	Renamed to getScenarioData
 */
function getScenarioData($id) {
	// Database variables
	global $db;
	global $tblScenarios;
	global $colScenarioData;
	global $colScenarioId;

	// Status codes
	global $E_MYSQL;
	global $E_SCEN_DOESNT_EXIST;
	global $E_SCEN_ID_NOT_RCVD;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_SCEN_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Scenario ID");
		return $E_SCEN_ID_NOT_RCVD;
	}

	$dbData = null;
	$query = "SELECT $colScenarioData FROM $tblScenarios WHERE $colScenarioId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($dbData);
		if($statement->fetch() == null) {
			createLog("warning", $E_SCEN_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Scenario does not exist", "ID: [$id]");
			$statement->close();
			return $E_SCEN_DOESNT_EXIST;
		} else {
			$statement->close();
			return $dbData;
		}			
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * getScenarioName
 * 
 * Retrieves Scenario Name from the database
 * 
 * @param 	string 		$id 	Scenario Identifier
 * @return 	string|int 			Scenario's name or error code
 * @since 				1.0.0
 * @deprecated 			2.0.0
 */
function getScenarioName($id) {
	return "";
}

/**
 * getUserEmail
 * 
 * Gets the user's email address according to what mode is passed.
 * Replaces getUserEmailByID in API v1
 * 
 * @param 	string 		$criteria 	Criteria to use as lookup value
 * @param 	string 		$mode 		Desired lookup value to get the email address.
 * @return 	string|int 				User's email address or error code
 * @since 				1.0.0
 * @since 				2.0.0 		Renamed to getUserEmail. Added mode functionality.
 */
function getUserEmail($criteria, $mode = "id") {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserEmail;
	global $colUserId;

	// Status codes
	global $E_ACNT_DOESNT_EXIST;
	global $E_CRITERIA_NOT_RCVD;
	global $E_MODE_INVALID;
	global $E_MYSQL;

	if(!isset($criteria) || $criteria == "") {
		createLog("warning", $E_CRITERIA_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Criteria");
		return $E_CRITERIA_NOT_RCVD;
	}

	// Determine the mode and generate the query
	$query = "";
	if($mode == "id") {
		$query = "SELECT $colUserEmail FROM $tblUsers WHERE $colUserId = ?"; 
	} else {
		createLog("warning", $E_MODE_INVALID, basename(__FILE__), __FUNCTION__, "Invalid mode received", "Mode: [$mode]");
		return $E_MODE_INVALID;
	}

	// Run the query
	$dbEmail = null;
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $criteria);
		$statement->execute();
		$statement->bind_result($dbEmail);
		if($statement->fetch() == null) {
			createLog("warning", $E_ACNT_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Account does not exist", "$mode: [$criteria]");
			$statement->close();
			return $E_ACNT_DOESNT_EXIST;
		} else {
			$statement->close();
			return $dbEmail;
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * getUserName
 * 
 * Gets the user's name from the database according to what mode is passed
 */
function getUserName($criteria, $mode = "id") {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserEmail;
	global $colUserFname;
	global $colUserId;
	global $colUserLname;

	// Status Codes
	global $E_ACNT_DOESNT_EXIST;
	global $E_CRITERIA_NOT_RCVD;
	global $E_MODE_INVALID;
	global $E_MYSQL;

	if(!isset($criteria) || $criteria == "") {
		createLog("warning", $E_CRITERIA_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Criteria");
	}

	// Determine mode and generate query
	$query = "";
	if($mode == "id") {
		$query = "SELECT $colUserFname, $colUserLname FROM $tblUsers WHERE $colUserId = ?";
	} else if($mode == "email") {
		$query = "SELECT $colUserFname, $colUserLname FROM $tblUsers WHERE $colUserEmail = ?";
	} else {
		createLog("warning", $E_MODE_INVALID, basename(__FILE__), __FUNCTION__, "Invalid mode received", "Mode: [$mode]");
		return $E_MODE_INVALID;
	}

	// Run the query
	$dbFname = null;
	$dbLname = null;
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $criteria);
		$statement->execute();
		$statement->bind_result($dbFname, $dbLname);
		if($statement->fetch() == null) {
			createLog("warning", $E_ACNT_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Account does not exist", "$mode: [$criteria]");
			$statement->close();
			return $E_ACNT_DOESNT_EXIST;
		} else {
			$statement->close();
			return array("fname" => $dbFname, "lname" => $dbLname);
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * grantAdmin
 * 
 * @param	string	$id		User Identifier
 * @return	int				Status code
 * @since 1.0.0
 */
function grantAdmin($id) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserAdmin;
	global $colUserId;

	// Status codes
	global $E_MYSQL;
	global $E_UNAUTHORIZED;
	global $E_USER_ID_NOT_RCVD;
	global $S_ADMIN_GRANTED;

	if(!isAdmin()) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to grant admin privileges", "Email: [".getUserEmail($_SESSION["id"])."]");
		return $E_UNAUTHORIZED;
	}

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	$query = "UPDATE $tblUsers SET $colUserAdmin = 1 WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->close();
		return $S_ADMIN_GRANTED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * isAdmin
 * 
 * Returns whether the user is an administrator. Uses their ID based on $_SESSION
 * 
 * @param 	void
 * @param	boolean				Returns whether or not a user is an administrator
 * @since				1.0.0
 */
function isAdmin() {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserAdmin;
	global $colUserId;

	// Status codes
	global $E_MYSQL;

	if(!isLoggedIn()) {
		return false;
	}

	$dbAdmin = null;
	$query = "SELECT $colUserAdmin FROM $tblUsers WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $_SESSION["id"]);
		$statement->execute();
		$statement->bind_result($dbAdmin);
		$statement->fetch();
		$statement->close();
		return ($dbAdmin == "1");
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return false;
	}
}

/**
 * isInMXMode
 * 
 * Returns whether or not the website is in maintenance mode.
 * Only an administrator can access the website while in maintenance mode.
 * 
 * @param 	void
 * @return 	bool
 * @since 				2.0.0
 */
function isInMXMode() {
	// Database variables
	global $db;
	global $tblSettings;
	global $colSettingsBoolValue;
	global $colSettingsName;

	// Status codes
	global $E_MYSQL;

	$dbMx = null;
	$settingName = "mx";
	$query = "SELECT $colSettingsBoolValue FROM $tblSettings WHERE $colSettingsName = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $settingName);
		$statement->execute();
		$statement->bind_result($dbMx);
		$statement->fetch();
		$statement->close();
		return ($dbMx == 1);
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return false;
	}
}

/**
 * isLoggedIn
 * 
 * Returns whether or not a client provides a valid SESSION
 * 
 * @param 	void
 * @return 	boolean 			Whether or not the SESSION is valid
 * @since 				1.0.0
 */
function isLoggedIn() {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserId;

	// Status codes
	global $E_MYSQL;
	global $E_SESS_INVALID;

	if(!isset($_SESSION["id"]) || $_SESSION["id"] == "") {
		return false;
	}

	$dbId = null;
	$query = "SELECT $colUserId FROM $tblUsers WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $_SESSION["id"]);
		$statement->execute();
		$statement->bind_result($dbId);
		if($statement->fetch() == null) {
			createLog("danger", $E_SESS_INVALID, basename(__FILE__), __FUNCTION__, "Session ID is invalid", "ID: [".$_SESSION."]");
			$statement->close();
			logout();
			return false;
		} else {
			$statement->close();
			return true;
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return false;
	}
}

/**
 * login
 * 
 * Provides for user authentication into the system. Provides security if 
 * too many failed attempts to log in are presented. Additionally, provides
 * the logging of locations, activity times, and IP addresses.
 * 
 * @param 	string 	$email 		User's Email Address
 * @param 	string 	$password 	User's Password
 * @return 	int 				Status code
 * @since 			1.0.0
 */
function login($email, $password) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserDisabled;
	global $colUserEmail;
	global $colUserId;
	global $colUserLoginAttempts;
	global $colUserPassword;

	// Status codes
	global $E_ACNT_DISABLED;
	global $E_ACNT_DOESNT_EXIST;
	global $E_EMAIL_NOT_RCVD;
	global $E_PSWD_INVALID;
	global $E_MYSQL;
	global $E_PSWD_NOT_RCVD;
	global $S_USER_AUTHENTICATED;

	if(!isset($email) || $email == "") {
		createLog("warning", $E_EMAIL_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Email Address");
		return $E_EMAIL_NOT_RCVD;
	}

	if(!isset($password) || $password == "") {
		createLog("warning", $E_PSWD_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Password");
		return $E_PSWD_NOT_RCVD;
	}

	//-----------------------------
	// Get user information
	//-----------------------------
	$dbId = null;
	$dbPassword = null;
	$dbLoginAttempts = null;
	$dbDisabled = null;
	$query = "SELECT $colUserId, $colUserPassword, $colUserLoginAttempts, $colUserDisabled FROM $tblUsers WHERE $colUserEmail = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($dbId, $dbPassword, $dbLoginAttempts, $dbDisabled);
		if($statement->fetch() == null) {
			createLog("warning", $E_ACNT_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Account does not exist", "Email: [$email]");
			$statement->close();
			return $E_ACNT_DOESNT_EXIST;
		} else {
			$statement->close();
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	//-----------------------------
	// Verify the password
	//-----------------------------
	if(password_verify($password, $dbPassword)) {
		// Set SESSION
		$_SESSION["id"] = $dbId;

		// Reset Login Attempts
		updateLoginAttempts($dbId, 0);

		// Log user's IP in database
		logIpAddress($dbId, $_SERVER["REMOTE_ADDR"]);

		// Update Last Login Time
		updateLoginTime($dbId);

		createLog("success", $S_USER_AUTHENTICATED, basename(__FILE__), __FUNCTION__, "User Logged into the system", "Email: [$email]");
		return $S_USER_AUTHENTICATED;
	} 
	//-----------------------------
	// Invalid login
	//-----------------------------
	else {
		createLog("warning", $E_PSWD_INVALID, basename(__FILE__), __FUNCTION__, "Invalid password", "Email: [$email]");

		// Increment the number of login attempts
		$dbLoginAttempts++;
		updateLoginAttempts($dbId, $dbLoginAttempts);

		if($dbLoginAttempts > 5) {
			createLog("danger", $E_ACNT_DISABLED, basename(__FILE__), __FUNCTION__, "More than 5 invalid login attempts. Disabling account.", "Email: [$email]");
			disableAccount($dbId, true);
			return $E_ACNT_DISABLED;
		}
		return $E_PSWD_INVALID;
	}
}

/**
 * logIpAddress
 * 
 * @param	string	$id		User Identifier
 * @param	string	$ip		IP Address
 * @return	int				Status code
 * @since			2.0.0	
 */
function logIpAddress($id, $ip) {
	// Database variables
	global $db;
	global $tblIplog;
	global $colIplogIp;
	global $colIplogUser;

	// Status codes
	global $E_IP_NOT_RCVD;
	global $E_MYSQL;
	global $E_USER_ID_NOT_RCVD;
	global $S_IP_LOGGED;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	if(!isset($ip) || $ip == "") {
		createLog("warning", $E_IP_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "IP Address");
		return $E_IP_NOT_RCVD;
	}

	// Get current IP addresses for user
	$dbIP = null;
	$newIP = true;
	$query = "SELECT $colIplogIp FROM $tblIplog WHERE $colIplogUser = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($dbIP);
		while($row = $statement->fetch()) {
			if($dbIP == $ip) {
				$newIP = false;
				break;
			}
		}
		$statement->close();
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	// Add the IP address only if it is a new one
	if($newIP) {
		$query = "INSERT INTO $tblIplog ($colIplogIp, $colIplogUser) VALUES (?, ?)";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("ss", $ip, $id);
			$statement->execute();
			$statement->close();
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
			return $E_MYSQL;
		}
	}
	return $S_IP_LOGGED;
}

/** logout
 * 
 * Destroys PHP SESSION, logging out a User
 * 
 * @param 	void
 * @return 	void
 * @since 			1.0.0
 */
function logout() {
	global $S_USER_LOGOUT;

	createLog("success", $S_USER_LOGOUT, basename(__FILE__), __FUNCTION__, "User Logged out", "Email: [".getUserEmail($_SESSION["id"])."]");

	foreach($_SESSION as $key => $data) {
		unset($_SESSION[$key]);
	}

	session_destroy();
}

/**
 * recoverAccount
 * 
 * Creates an account recovery token for forgotten passwords and disabled accounts.
 * 
 * @param 	string 		$email 		User Account's Email Address
 * @return 	string|int 				Recovery token or error code
 * @since 				1.0.0
 */
function recoverAccount($email) {
	// Database variables
	global $db;
	global $tblRecovery;
	global $tblUsers;
	global $colRecoveryEmail;
	global $colRecoveryId;
	global $colRecoveryToken;
	global $colUserEmail;
	
	// Status codes
	global $E_ACNT_DOESNT_EXIST;
	global $E_EMAIL_NOT_RCVD;
	global $E_MYSQL;

	if(!isset($email) || $email == "") {
		createLog("warning", $E_EMAIL_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Email address");
		return $E_EMAIL_NOT_RCVD;
	}

	// Check that the email is in the system
	$dbEmail = null;
	$query = "SELECT $colUserEmail FROM $tblUsers WHERE $colUserEmail =?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($dbEmail);
		if($statement->fetch() == null) {
			createLog("warning", $E_ACNT_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Account does not exist", "Email: [$email]");
			$statement->close();
			return $E_ACNT_DOESNT_EXIST;
		}
		$statement->close();
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	// Check if there is already a token in the system
	$dbRecoveryId = null;
	$tokenExists = true;
	$query = "SELECT $colRecoveryId FROM $tblRecovery WHERE $colRecoveryEmail = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($dbRecoveryId);
		if($statement->fetch() == null) {
			$tokenExists = false;
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	$id = createUUID();
	$token = createKey();

	if($tokenExists) {
		$query = "UPDATE $tblRecovery SET $colRecoveryToken = ? WHERE $colRecoveryEmail = ?";
	} else {
		$query = "INSERT INTO $tblRecovery ($colRecoveryId, $colRecoveryEmail, $colRecoveryToken) VALUES (?, ?, ?)";
	}

	if($statement = $db->prepare($query)) {
		if($tokenExists) {
			$statement->bind_param("ss", $token, $email);
		} else {
			$statement->bind_param("sss", $id, $email, $token);
		}
		$statement->execute();
		$statement->close();
		return $token;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * revokeAdmin
 * 
 * Revokes administrative privileges from a user
 * 
 * @param 	string 	$id 		User Identifier
 * @return 	int 				status code
 * @since 			1.0.0
 */
function revokeAdmin($id) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserAdmin;
	global $colUserId;

	// Status codes
	global $E_MYSQL;
	global $E_USER_ID_NOT_RCVD;
	global $E_UNAUTHORIZED;
	global $S_ADMIN_REVOKED;

	if(!isAdmin()) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to revoke admin privileges", "Email: [".getUserEmail($_SESSION["id"])."]");
		return $E_UNAUTHORIZED;
	}

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	$query = "UPDATE $tblUsers SET $colUserAdmin = 0 WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->close();
		return $S_ADMIN_REVOKED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * saveScenario
 * 
 * Receives information about a scenario and saves it to the database
 * 
 * @param 	string 	$user 	User Identifier
 * @param 	string 	$name 	Scenario Name
 * @param 	string 	$data 	JSON Encoded Scenario Data
 * @return 	int 			Status code
 * @since 			1.0.0
 */
function saveScenario($user, $name, $data) {
	// Database variables
	global $db;
	global $tblScenarios;
	global $colScenarioCreated;
	global $colScenarioData;
	global $colScenarioId;
	global $colScenarioName;
	global $colScenarioUser;

	// Status codes
	global $E_MYSQL;
	global $E_SCEN_DATA_NOT_RCVD;
	global $E_USER_ID_NOT_RCVD;
	global $S_SCEN_SAVED;

	if(!isset($user) || $user == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Scenario ID");
		return $E_USER_ID_NOT_RCVD;
	}

	if(!isset($name) || $name == "") {
		$name = "Scenario ".date("Y-m-d H:i:s");
	}

	if(!isset($data) || $data == "") {
		createLog("warning", $E_SCEN_DATA_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Scenario Data");
		return $E_SCEN_DATA_NOT_RCVD;
	}

	$id = createUUID();
	$date = date("Y-m-d H:i:s");

	$query = "INSERT INTO $tblScenarios ($colScenarioId, $colScenarioName, $colScenarioUser, $colScenarioCreated, $colScenarioData) VALUES (?, ?, ?, ?, ?)";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("sssss", $id, $name, $user, $date, $data);
		$statement->execute();
		$statement->close();
		createLog("success", $S_SCEN_SAVED, basename(__FILE__), __FUNCTION__, "Scenario Saved", "ID: [$id]");
		return $S_SCEN_SAVED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/** 
 * updateLoginAttempts
 * 
 * @param	string	$id			User Identifier
 * @param	int		$attempts	Number of Login Attempts
 * @return	int					Status code
 * @since			2.0.0
 */
function updateLoginAttempts($id, $attempts) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserId;
	global $colUserLoginAttempts;

	// Status codes
	global $E_ATTEMPTS_NOT_RCVD;
	global $E_MYSQL;
	global $E_USER_ID_NOT_RCVD;
	global $S_ATTEMPTS_UPDATED;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	if(!isset($attempts) || $attempts == "") {
		createLog("warning", $E_ATTEMPTS_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Login Attempts");
		return $E_ATTEMPTS_NOT_RCVD;
	}

	$query = "UPDATE $tblUsers SET $colUserLoginAttempts = ? WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("is", $attempts, $id);
		$statement->execute();
		$statement->close();
		return $S_ATTEMPTS_UPDATED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * updateLoginTime
 * 
 * @param	string	$id		User Identifier
 * @param	string	$date	Date of Login
 * @return	int				Status code
 * @since			2.0.0
 */
function updateLoginTime($id) {
	// Database variables
	global $db;
	global $tblUsers;
	global $colUserId;
	global $colUserLastLogin;

	// Status codes
	global $E_MYSQL;
	global $E_USER_ID_NOT_RCVD;
	global $S_LOGIN_DTG_UPDATED;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_USER_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "User ID");
		return $E_USER_ID_NOT_RCVD;
	}

	$date = date("Y-m-d H:i:s");

	$query = "UPDATE $tblUsers SET $colUserLastLogin = ? WHERE $colUserId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ss", $date, $id);
		$statement->execute();
		$statement->close();
		return $S_LOGIN_DTG_UPDATED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * updateScenario
 * 
 * Overwrites a saved scenario with an updated version from a User
 * 
 * @param 	string 	$id 	Scenario Identifier
 * @param 	string 	$name 	New Scenario Name
 * @param 	string 	$data 	New Scenario Data
 * @return 	int 			Status code
 * @since 			1.0.0
 */
function updateScenario($id, $name, $data) {
	// Database variables
	global $db;
	global $tblScenarios;
	global $colScenarioCreated;
	global $colScenarioData;
	global $colScenarioId;
	global $colScenarioName;
	global $colScenarioUser;

	// Status codes
	global $E_MYSQL;
	global $E_SCEN_DATA_NOT_RCVD;
	global $E_SCEN_DOESNT_EXIST;
	global $E_SCEN_ID_NOT_RCVD;
	global $E_UNAUTHORIZED;
	global $S_SCEN_UPDATED;

	if(!isset($id) || $id == "") {
		createLog("warning", $E_SCEN_ID_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Scenario ID");
		return $E_SCEN_ID_NOT_RCVD;
	}

	if(!isset($name) || $name == "") {
		$name = "Scenario ".date("Y-m-d H:i:s");
	}

	if(!isset($data) || $data == "") {
		createLog("warning", $E_SCEN_DATA_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Scenario Data");
		return $E_SCEN_DATA_NOT_RCVD;
	}

	// Get the owner of the scenario
	$dbUser = null;
	$query = "SELECT $colScenarioUser FROM $tblScenarios WHERE $colScenarioId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($dbUser);
		if($statement->fetch() == null) {
			createLog("warning", $E_SCEN_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Scenario does not exist", "ID: [$id]");
			$statement->close();
			return $E_SCEN_DOESNT_EXIST;
		} else {
			$statement->close();
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	if($dbUser != $_SESSION["id"]) {
		createLog("warning", $E_UNAUTHORIZED, basename(__FILE__), __FUNCTION__, "User not authorized to overwrite scenario. ID: [$id]", "Email: [".getUserEmail($dbUser)."]");
		return $E_UNAUTHORIZED;
	}

	$date = date("Y-m-d H:i:s");

	$query = "UPDATE $tblScenarios SET $colScenarioName = ?, $colScenarioCreated = ?, $colScenarioData = ? WHERE $colScenarioId = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ssss", $name, $date, $data, $id);
		$statement->execute();
		$statement->close();
		createLog("success", $S_SCEN_UPDATED, basename(__FILE__), __FUNCTION__, "Updated Scenario", "ID: [$id]");
		return $S_SCEN_UPDATED;
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}
}

/**
 * validateRecoveryToken
 * 
 * Validates an Account Recovery Token.
 * 
 * @param 	string 	$email 	User Account Email Address
 * @param 	string 	$token 	Recovery Token
 * @return 	int 	Status 	code
 * @since 			1.0.0
 */
function validateRecoveryToken($email, $token) {
	// Database variables
	global $db;
	global $tblRecovery;
	global $colRecoveryEmail;
	global $colRecoveryCreated;
	global $colRecoveryToken;

	// Status codes
	global $E_MYSQL;
	global $E_EMAIL_NOT_RCVD;
	global $E_TOKEN_EXPIRED;
	global $E_TOKEN_INVALID;
	global $E_TOKEN_NOT_RCVD;
	global $S_TOKEN_VALID;

	if(!isset($email) || $email == "") {
		createLog("warning", $E_EMAIL_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Email Address");
	}

	if(!isset($token) || $token == "") {
		createLog("warning", $E_TOKEN_NOT_RCVD, basename(__FILE__), __FUNCTION__, "Data not received", "Account Recovery Token");
	}

	// Confirm that the token is valid
	$dbCreated = null;
	$query = "SELECT $colRecoveryCreated FROM $tblRecovery WHERE $colRecoveryEmail = ? AND $colRecoveryToken = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ss", $email, $token);
		$statement->execute();
		$statement->bind_result($dbCreated);
		if($statement->fetch() == null) {
			createLog("warning", $E_TOKEN_INVALID, basename(__FILE__), __FUNCTION__, "Invalid Email / Token pairing", "Email: [$email]. Token: [$token]");
			$statement->close();
			return $E_TOKEN_INVALID;
		} else {
			$statement->close();
		}
	} else {
		createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
		return $E_MYSQL;
	}

	// Confirm that the token is not expired
	$currentTime = time();
	$checkTime = strtotime("+30 minutes", strtotime($dbCreated));

	if($currentTime > $checkTime) {
		createLog("warning", $E_TOKEN_EXPIRED, basename(__FILE__), __FUNCTION__, "Token Expired", "Email: [$email]. Token: [$token]");
		deleteRecoveryToken($email);
		return $E_TOKEN_EXPIRED;
	} else {
		return $S_TOKEN_VALID;
	}
}
?>