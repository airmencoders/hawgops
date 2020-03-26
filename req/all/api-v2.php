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
		if($statement->execute()) {
			createLog("success", $S_PSWD_CHANGED, basename(__FILE__), __FUNCTION__, "Password changed", "Email: [$email]");
			$statement->close();
			return $S_PSWD_CHANGED;
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to change password for email [".getUserEmail("id", $id)."]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			// Do Nothing
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to confirm if account already exists [$email]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			createLog("success", $S_ACNT_CREATED, basename(__FILE__), __FUNCTION__, "Account Created", "Email: [$email]");
			$statement->close();
			return $S_ACNT_CREATED;
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to create account [$email]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
 * @param	string	$email	User Account Email Address
 * @return	int				Status code
 * @since			2.0.0
 */
function deleteRecoveryToken($email) {
	// Database variables
	global $db;
	global $tblRecovery;
	global $colRecoveryEmail;

	// Status Codes
	global $E_EMAIL_NOT_RCVD;
	global $E_MYSQL;
	global $S_TOKEN_DELETED;

	if(!isset($email) || $email == "") {
		createLog("warning", $E_EMAIL_NOT_RCVD, "API", "deleteRecoveryToken", "Data not received", "Email Address");
		return $E_EMAIL_NOT_RCVD;
	}

	// Delete the recovery token
	$query = "DELETE FROM $tblRecovery WHERE $colRecoveryEmail = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		if($statement->execute()) {
			createLog("success", $S_TOKEN_DELETED, "API", "deleteRecoveryToken", "Token Deleted", "Email: [$email]");
			$statement->close();
			return $S_TOKEN_DELETED;
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to Delete Recovery Token. Email: [$email]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
	} else {
		createLog("warning", $E_MYSQL, "API", "deleteRecoveryToken", "Failed to prepare query [$query]", $db->error." (".$db->errno.")");
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
		if($statement->execute()) {
			// Do nothing
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to retrieve Scenario Owner", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			createLog("success", $S_SCEN_DELETED, basename(__FILE__), __FUNCTION__, "Scenario Deleted", "ID: [$id]");
			$statement->close();
			return $S_SCEN_DELETED;
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to delete scenario [$id]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			createLog("success", $S_ACNT_DISABLED, basename(__FILE__), __FUNCTION__, "Account Disabled", "Email: [".getUserEmail("id", $id)."]");
			$statement->close();
			return $S_ACNT_DISABLED;
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to disable account [".getUserEmail("id", $id)."]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			createLog("success", $S_ACNT_ENABLED, basename(__FILE__), __FUNCTION__, "Account Enabled", "Email: [".getUserEmail("id", $id)."]");

			// Delete the token from the database
			$deleteStatus = deleteRecoveryToken($email);
			if($deleteStatus != $S_TOKEN_DELETED) {
				createLog("warning", $deleteStatus, basename(__FILE__), __FUNCTION__, "Failed to delete token from database");
				$statement->close();
				return $deleteStatus;
			} else {
				$statement->close();
				return $S_ACNT_ENABLED;
			}
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to Enable account [".getUserEmail("id", $id)."]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			$statement->bind_result($dbId,
									$dbEmail,
									$dbFname,
									$dbLname,
									$dbDisabled,
									$dbAdmin,
									$dbJoined,
									$dbLastLogin);
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
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to retrieve user information", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
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
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to retrieve IP Log. Email: [".getUserEmail("id", $id)."]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
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
		if($statement->execute()) {
			$statement->bind_result($dbCount);
			$statement->fetch();
			$statement->close();
			return $dbCount;
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to retrieve scenario count for Email [".getUserEmail("id", $id)."]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
		}
		
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
		if($statement->execute()) {
			$statement->bind_result($dbData);
			if($statement->fetch()) {
				return $dbData;
			} else {
				createLog("warning", $E_SCEN_DOESNT_EXIST, basename(__FILE__), __FUNCTION__, "Scenario does not exist", "ID: [$id]");
				$statement->close();
				return $E_SCEN_DOESNT_EXIST;
			}			
		} else {
			createLog("danger", $E_MYSQL, basename(__FILE__), __FUNCTION__, "Failed to retrieve Scenario [$id]", $statement->error." (".$statement->errno.")");
			$statement->close();
			return $E_MYSQL;
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
 * @param 	string 		$mode 	Desired lookup value to get the email address.
 * @param 	string 		$data 	Value to use as lookup value
 * @return 	string|int 			User's email address or error code
 * @since 				1.0.0
 * @since 				2.0.0 	Renamed to getUserEmail. Added mode functionality.
 */
function getUserEmail($mode, $data) {

}

/**
 * getUserName
 * 
 * Gets the user's name from the database according to what mode is passed
 */
function getUserName($mode, $data) {

}

/**
 * isAdmin
 * 
 * Returns whether the user is an administrator
 * 
 * @param 	void
 * @param	boolean				Returns whether or not a user is an administrator
 * @since				1.0.0
 */
function isAdmin() {

}

/**
 * isInMXMode
 * 
 * Returns whether or not the website is in maintenance mode.
 * Only an administrator can access the website while in maintenance mode.
 * 
 * @param 	void
 * @return 	boolean
 * @since 				2.0.0
 */
function isInMXMode() {

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

}

/**
 * saveScenario
 * 
 * Receives information about a scenario and saves it to the database
 * 
 * @param 	string 	$id 	User Identifier
 * @param 	string 	$name 	Scenario Name
 * @param 	string 	$data 	JSON Encoded Scenario Data
 * @return 	int 			Status code
 * @since 			1.0.0
 */
function saveScenario($id, $name, $data) {

}

/**
 * updateScenario
 * 
 * Overwrites a saved scenario with an updated version from a User
 * 
 * @param 	string 	$user 	User Identifier
 * @param 	string 	$id 	Scenario Identifier
 * @param 	string 	$name 	New Scenario Name
 * @param 	string 	$data 	New Scenario Data
 * @return 	int 			Status code
 * @since 			1.0.0
 */
function updateScenario($user, $id, $name, $data) {

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
	global $S_TOKEN_VALID;
	return $S_TOKEN_VALID;
}
?>