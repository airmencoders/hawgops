<?php
session_start();
//==============================================================
// CONSTANTS
//==============================================================

$logPath = "/opt/bitnami/apache2/htdocs/logs/";
$filename = date("Y-m-d").".txt";

//Check for log, keep size under control, and open log file
$log = $logPath.$filename;

if(file_exists($log) && (filesize($log) > 1500000)) {
	rename ($log, $log.".old");
}    

// Mode: a - Write only, pointer at end of file
// Mode: b - Binary flag, no Windows translation of \n
$logFile = fopen($log, "ab");

//Open the logfile or display an error if there was one
if(!$logFile) {
	echo "Unable to open log file: [".$log."]";
	exit;
}

//==============================================================
// FUNCTIONS
//==============================================================

/**
 * changePassword()
 *
 * @param String $id User ID
 * @param String $email User Email (Only used when resetting the password from a recoverAccount situation)
 * @param String $newPassword User password
 *
 * @return Integer Status code
 */
function changePassword($id, $email, $token, $newPassword) {
	global $db;
	global $tbl_reset;
	global $tbl_users;
	global $col_reset_email;
	global $col_user_id;
	global $col_user_password;

	global $API_CHANGE_PASSWORD_KEYS_NOT_RECEIVED;
	global $API_CHANGE_PASSWORD_TOKEN_NOT_RECEIVED;
	global $API_CHANGE_PASSWORD_NEW_PASSWORD_NOT_RECEIVED;
	global $API_CHANGE_PASSWORD_COULD_NOT_GET_USER_ID;
	global $API_CHANGE_PASSWORD_COULD_NOT_ENABLE_ACCOUNT;
	global $API_CHANGE_PASSWORD_PASSWORD_CHANGED;
	global $API_ENABLE_ACCOUNT_ACCOUNT_ENABLED;
	global $ERROR_MYSQL;

	if((!isset($id) || $id == "") && (!isset($email) || $email == "")) {
		createLog("warning", $API_CHANGE_PASSWORD_KEYS_NOT_RECEIVED, "API", "changePassword", "Data not received", "Email Address and User ID");
		return $API_CHANGE_PASSWORD_KEYS_NOT_RECEIVED;
	}

	if(isset($email) && $email != "") {
		if(!isset($token) || $token == "") {
			createLog("warning", $API_CHANGE_PASSWORD_TOKEN_NOT_RECEIVED, "API", "changePassword", "Data not received", "Token");
			return $API_CHANGE_PASSWORD_TOKEN_NOT_RECEIVED;
		}
	}

	if(!isset($newPassword) || $newPassword == "") {
		createLog("warning", $API_CHANGE_PASSWORD_NEW_PASSWORD_NOT_RECEIVED, "API", "changePassword", "Data not received", "New Password");
		return $API_CHANGE_PASSWORD_NEW_PASSWORD_NOT_RECEIVED;
	}

	// Assume that we've already done the work to confirm the email and token by this point
	if(isset($email)) {
				// Re-enable the user's account
		// First get the user ID (this assumes that $id is null because this occurs when in a recover-account status)
		$id = getUserIDByEmail($email);
		if(is_numeric($id)) {
			createLog("warning", $API_CHANGE_PASSWORD_COULD_NOT_GET_USER_ID, "API", "changePassword", "Recovering account, could not get user's ID to re-enable account", "Email:[$email] // Code: [$id]");
			return $id; // Return whatever getUserIDByEmail() returned
		}

		$enabled = enableAccount($id, true, $email, $token);
		if($enabled != $API_ENABLE_ACCOUNT_ACCOUNT_ENABLED) {
			createLog("warning", $API_CHANGE_PASSWORD_COULD_NOT_ENABLE_ACCOUNT, "API", "changePassword", "Recovering account, could not enable user's account", "Email: [$email] // ID: [$id] // Code: [$enabled]");
			return $enabled; // Return whatever enableAccount() returned
		}

		// Remove the user's token from the database
		$query = "DELETE FROM $tbl_reset WHERE $col_reset_email = ?";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("s", $email);
			$statement->execute();
			$statement->close();
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "changePassword", "Failed to prepare query", $db->error." (".$db->errno.")");
			return $ERROR_MYSQL;
		}
	}

	// Assuming at this point that either the recover-password-do or the change-password-do have already enforced password rules
	$password = password_hash($newPassword, PASSWORD_DEFAULT);

	// Change the password
	// By this point, $id is either set from the function, or set when doing all the previous information with the getUserIDByEmail() function
	$query = "UPDATE $tbl_users SET $col_user_password = ? WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ss", $password, $id);
		$statement->execute();
		$statement->close();

		return $API_CHANGE_PASSWORD_PASSWORD_CHANGED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "changePassword", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/** 
 * closeLogs()
 * 
 * Flush buffers and close the log file.
 */
function closeLogs() {
	global $logFile;
	fclose($logFile);
	exit;
}

/**
 * createAccount
 *
 * @param String $fname User's first name
 * @param String $lname User's last name
 * @param String $email User's email address
 * @param String $password User's password
 *
 * @return Integer Response code according to any error that occurred or success of function.
 *
 * Receive data for a user's account and attempt to create an account for that user.
 * Only allows for one user account per email address to be created.
 * Assumes that the "create-account-do.php" already performed necessary logic to ensure passwords match and meet security criteria.
 */
function createAccount($fname, $lname, $email, $password) {
	global $db;
	global $tbl_users;
	global $col_user_fname;
	global $col_user_lname;
	global $col_user_email;
	global $col_user_password;
	global $col_user_id;
	global $col_user_joined;
	
	global $API_CREATE_ACCOUNT_FIRST_NAME_NOT_RECEIVED;
	global $API_CREATE_ACCOUNT_LAST_NAME_NOT_RECEIVED;
	global $API_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED;
	global $API_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED;
	global $API_CREATE_ACCOUNT_ACCOUNT_EXISTS;
	global $API_CREATE_ACCOUNT_ACCOUNT_CREATED;
	global $ERROR_MYSQL;
	
	// Ensure parameters are received
	if(!isset($fname) || $fname == "") {
		createLog("warning", $API_CREATE_ACCOUNT_FIRST_NAME_NOT_RECEIVED, "API", "createAccount", "Data not received", "First Name");
		return $API_CREATE_ACCOUNT_FIRST_NAME_NOT_RECEIVED;
	}
	
	if(!isset($lname) || $lname == "") {
		createLog("warning", $API_CREATE_ACCOUNT_LAST_NAME_NOT_RECEIVED, "API", "createAccount", "Data not received", "Last Name");
		return $API_CREATE_ACCOUNT_LAST_NAME_NOT_RECEIVED;
	}
	
	if(!isset($email) || $email == "") {
		createLog("warning", $API_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED, "API", "createAccount", "Data not received", "Email Address");
		return $API_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED;
	}
	
	if(!isset($password) || $password == "") {
		createLog("warning", $API_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED, "API", "createAccount", "Data not received", "Password");
		return $API_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED;
	}
	
	$email = strtolower($email);
	$date = date("Y-m-d H:i:s");
	$id = createKey();
	
	// Hash the password
	$password = password_hash($password, PASSWORD_DEFAULT);
	
	// Confirm that no account currently exists for the provided email address
	$query = "SELECT $col_user_id FROM $tbl_users WHERE $col_user_email = ?";
	
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($db_userID);
		$statement->fetch();
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "createAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
	
	if($db_userID != null) {
		createLog("warning", $API_CREATE_ACCOUNT_ACCOUNT_EXISTS, "API", "createAccount", "Account already exists", "[$email]");
		return $API_CREATE_ACCOUNT_ACCOUNT_EXISTS;
	}
	
	// Create the user account
	$query = "INSERT INTO $tbl_users ($col_user_id, $col_user_fname, $col_user_lname, $col_user_email, $col_user_password, $col_user_joined) VALUES (?, ?, ?, ?, ?, ?)";
	
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ssssss", $id, $fname, $lname, $email, $password, $date);
		if($statement->execute()) {
			// Do Nothing
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "createAccount", "Account creation failed", $db->error." (".$db->errno.")");		
		}
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "createAccount", "Query prepartion failed", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
	
	// Ensure that the account was created
	$query = "SELECT $col_user_id FROM $tbl_users WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($db_userID);
		$statement->fetch();
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "createAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
	
	// User account was created if the user ID is no longer null
	if($db_userID != null) {
		createLog("success", $API_CREATE_ACCOUNT_ACCOUNT_CREATED, "API", "createAccount", "Account created", "[$email]");
		return $API_CREATE_ACCOUNT_ACCOUNT_CREATED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "createAccount", "Account failed to be created", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}	
}

/**
 * createKey()
 *
 * @return String Hashed string to be used as database column ID
 */
function createKey() {
	// Character Seed
	$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
	
	// Set seed string length
	$length = 25;
	
	// Initialize some variables
	$charactersLength = strlen($characters);
	$string = "";
	
	// Create the seed string
	for($i = 0; $i < $length; $i++) {
		$string .= $characters[rand(0, $charactersLength - 1)];
	}
	
	// Prepend the time (further randomizes hash)
	$string = date("U").$string;
	
	// Hash the seed string
	$key = hash("sha256", $string);
	
	// Return the hashed key
	return $key;
}

function createLog($level, $code, $caller, $function, $activity, $details) {
	global $logFile;
	
	$date = date("Y-m-d h:i:s");
	if(!isset($_SESSION["id"]) || $_SESSION["id"] == "") {
		$user = "-";
	} else {
		$user = getUserEmailByID($_SESSION["id"]);
	}
	
	$array = array("datetime"=>$date, "user"=>$user, "ip"=>$_SERVER["REMOTE_ADDR"], "caller"=>$caller, "function"=>$function, "level"=>$level, "code"=>$code, "activity"=>$activity, "details"=>$details, "location"=>"-", "lat"=>"-", "lng"=>"-");
	
	$json = json_encode($array);
	fwrite($logFile,$json.",");
}

/**
 * deleteScenario()
 *
 * @param String $sid Scenario ID
 *
 * @return Integer Status code
 */
function deleteScenario($sid) {
	global $db;
	global $tbl_scenarios;
	global $col_scenario_user;
	global $col_scenario_id;
	
	global $API_DELETE_SCENARIO_ID_NOT_RECEIVED;
	global $API_DELETE_SCENARIO_SCENARIO_DOES_NOT_EXIST;
	global $API_DELETE_SCENARIO_SCENARIO_DELETED;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isset($sid) || $sid == "") {
		createLog("warning", $API_DELETE_SCENARIO_ID_NOT_RECEIVED, "API", "deleteScenario", "Data not received", "Scenario ID");
		return $API_DELETE_SCENARIO_ID_NOT_RECEIVED;
	}
	
	// Ensure that the scenario is owned by the user, or is an admin
	// Also ensures that the scenario exists
	$query = "SELECT $col_scenario_user FROM $tbl_scenarios WHERE $col_scenario_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $sid);
		$statement->execute();
		$statement->bind_result($db_user);
		
		if($statement->fetch() == null) {
			createLog("warning", $API_DELETE_SCENARIO_SCENARIO_DOES_NOT_EXIST, "API", "deleteScenario", "Scenario does not exist", "[$sid]");
			$statement->close();
			return $API_DELETE_SCENARIO_SCENARIO_DOES_NOT_EXIST;
		}
	}
	$statement->close();
	
	if(!isAdmin() && $db_user != $_SESSION["id"]) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "deleteScenario", "User unauthorized to delete scenario [$sid]", getUserEmailByID($_SESSION["id"]));
		return $ERROR_UNAUTHORIZED;
	}

	// Delete the scenario
	$query = "DELETE FROM $tbl_scenarios WHERE $col_scenario_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $sid);
		$statement->execute();
		$statement->close();
		createLog("success", $API_DELETE_SCENARIO_SCENARIO_DELETED, "API", "deleteScenario", "Scenario Deleted", "-");
		return $API_DELETE_SCENARIO_SCENARIO_DELETED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "deleteScenario", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * disableAccount()
 *
 * @param String $id User ID
 *
 * Disables a user's account due to too many login attempts.
 */
function disableAccount($uid) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_disabled;
	
	global $API_DISABLE_ACCOUNT_ACCOUNT_DISABLED;
	global $API_DISABLE_ACCOUNT_ID_NOT_RECEIVED;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "disableAccount", "User not authorized to disable accounts", getUserEmailByID($_SESSION["id"]));
		return $ERROR_UNAUTHORIZED;
	}
	
	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_DISABLE_ACCOUNT_ID_NOT_RECEIVED, "API", "disableAccount", "Data not received", "User ID");
		return $API_DISABLE_ACCOUNT_ID_NOT_RECEIVED;
	}
	
	$query = "UPDATE $tbl_users SET $col_user_disabled = 1 WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->close();
		
		createLog("success", $API_DISABLE_ACCOUNT_ACCOUNT_DISABLED, "API", "disableAccount", "Account Disabled", getUserEmailByID($uid));
		return $API_DISABLE_ACCOUNT_ACCOUNT_DISABLED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "disableAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * enableAccount()
 *
 * @param String $id User ID
 *
 * Enable's a user's account
 */
function enableAccount($uid, $recoveryMode, $email, $token) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_disabled;
	
	global $API_ENABLE_ACCOUNT_ACCOUNT_ENABLED;
	global $API_ENABLE_ACCOUNT_ID_NOT_RECEIVED;
	global $API_ENABLE_ACCOUNT_MODE_NOT_SET;
	global $API_ENABLE_ACCOUNT_EMAIL_NOT_RECEIVED;
	global $API_ENABLE_ACCOUNT_TOKEN_NOT_RECEIVED;
	global $API_VALIDATE_RECOVERY_TOKEN_TOKEN_VALID;
	global $API_ENABLE_ACCOUNT_INVALID_TOKEN;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	

	if(!isset($recoveryMode)) {
		createLog("warning", $API_ENABLE_ACCOUNT_MODE_NOT_SET, "API", "enableAccount", "Recovery mode not set", "-");
		return $API_ENABLE_ACCOUNT_MODE_NOT_SET;
	}

	if($recoveryMode) {
		if(!isset($email) || $email == "") {
			createLog("warning", $API_ENABLE_ACCOUNT_EMAIL_NOT_RECEIVED, "API", "enableAccount", "[RECOVERY MODE] Data not received", "Email Address");
			return $API_ENABLE_ACCOUNT_EMAIL_NOT_RECEIVED;
		}

		if(!isset($token) || $token == "") {
			createLog("warning", $API_ENABLE_ACCOUNT_TOKEN_NOT_RECEIVED, "API", "enableAccount", "[RECOVERY MODE] Data not received", "Token");
			return $API_ENABLE_ACCOUNT_TOKEN_NOT_RECEIVED;
		}

		// validate the token
		$validToken = validateRecoveryToken($email, $token);
		if($validToken != $API_VALIDATE_RECOVERY_TOKEN_TOKEN_VALID) {
			createLog("warning", $API_ENABLE_ACCOUNT_INVALID_TOKEN, "API", "enableAccount", "[RECOVERY MODE] Invalid token combination", "Email: [$email] // Token: [$token] // Code: [$validToken]");
			return $validToken;
		}
	} else {
		if(!isAdmin()) {
			createLog("warning", $ERROR_UNAUTHORIZED, "API", "enableAccount", "User not authorized to enable accounts", getUserEmailByID($_SESSION["id"]));
			return $ERROR_UNAUTHORIZED;
		}
		
		if(!isset($uid) || $uid == "") {
			createLog("warning", $API_ENABLE_ACCOUNT_ID_NOT_RECEIVED, "API", "enableAccount", "Data not received", "User ID");
			return $API_ENABLE_ACCOUNT_ID_NOT_RECEIVED;
		}
	}
	
	$query = "UPDATE $tbl_users SET $col_user_disabled = 0 WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->close();
		
		createLog("success", $API_ENABLE_ACCOUNT_ACCOUNT_ENABLED, "API", "enableAccount", "Account Enabled", getUserEmailByID($uid));
		return $API_ENABLE_ACCOUNT_ACCOUNT_ENABLED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "enableAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * getScenario
 *
 * @param String $uid User ID
 * @param String $sid Scenario ID
 * 
 * @return String JSON encoded data to be loaded into the map
 * @return Integer Error Code
 */ 
function getScenario($sid) {
	global $db;
	global $tbl_scenarios;
	
	global $col_scenario_id;
	global $col_scenario_data;
	
	global $API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED;
	global $API_GET_SCENARIO_SCENARIO_DOES_NOT_EXIST;
	global $ERROR_UNAUTHORIZED;
	
	if(!isset($sid) || $sid == "") {
		createLog("warning", $API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED, "API", "getScenario", "Data not received", "Scenario ID");
		return $API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED;
	}
		
	$query = "SELECT $col_scenario_data FROM $tbl_scenarios WHERE $col_scenario_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $sid);
		$statement->execute();
		$statement->bind_result($db_data);
		
		if($statement->fetch() == null) {
			createLog("warning", $API_GET_SCENARIO_SCENARIO_DOES_NOT_EXIST, "API", "getScenario", "Scenario does not exist", "[$sid]");
			return $API_GET_SCENARIO_SCENARIO_DOES_NOT_EXIST;
		}
		
		$statement->close();
		return $db_data;		
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getScenario", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

function getScenarioName($sid) {
	global $db;
	global $tbl_scenarios;
	global $col_scenario_id;
	global $col_scenario_name;

	global $ERROR_MYSQL;
	global $API_GET_SCENARIO_NAME_SCENARIO_ID_NOT_RECEIVED;
	global $API_GET_SCENARIO_NAME_SCENARIO_DOES_NOT_EXIST;

	if(!isset($sid) || $sid == "") {
		createLog("warning", $API_GET_SCENARIO_NAME_SCENARIO_ID_NOT_RECEIVED, "API", "getScenarioName", "Data not received", "Scenario ID");
		return $API_GET_SCENARIO_NAME_SCENARIO_ID_NOT_RECEIVED;
	}

	$query = "SELECT $col_scenario_name FROM $tbl_scenarios WHERE $col_scenario_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $sid);
		$statement->execute();
		$statement->bind_result($db_name);

		if($statement->fetch() == null) {
			$statement->close();
			createLog("warning", $API_GET_SCENARIO_NAME_SCENARIO_DOES_NOT_EXIST, "API", 
			"getScenarioName", "Scenario does not exist", "ID: [$sid]");
			return $API_GET_SCENARIO_NAME_SCENARIO_DOES_NOT_EXIST;
		}

		$statement->close();
		return $db_name;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getScenarioName", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

function getNumberOfScenariosByUser($uid) {
	global $db;
	global $tbl_scenarios;
	global $col_scenario_id;
	global $col_scenario_user;
	
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "getNumberOfScenariosByUser", "User not authorized to count number of scenarios", getUserEmailByID($_SESSION["id"]));
		return $ERROR_UNAUTHORIZED;
	}
	
	$query = "SELECT COUNT($col_scenario_id) AS NUM FROM $tbl_scenarios WHERE $col_scenario_user = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->bind_result($db_num_scenarios);
		$statement->fetch();
		$statement->close();
		return $db_num_scenarios;
	} else {
		createLog("danager", $ERROR_MYSQL, "API", "getNumberOfScenariosByuser", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * getUsers
 *
 * @return Array Array of users
 */
function getAllUsers() {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_email;
	global $col_user_fname;
	global $col_user_lname;
	global $col_user_disabled;
	global $col_user_admin;
	global $col_user_joined;
	global $col_user_last_login;
	
	global $ERROR_MYSQL;
	global $ERROR_UNAUTHORIZED;

	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "getAllUsers", "User not authorized to view all users", getUserEmailByID($_SESSION["id"]));
		return $ERROR_UNAUTHORIZED;
	}
	
	$query = "SELECT $col_user_id, $col_user_email, $col_user_fname, $col_user_lname, $col_user_disabled, $col_user_admin, $col_user_joined, $col_user_last_login FROM $tbl_users ORDER BY $col_user_joined";
	
	if($statement = $db->prepare($query)) {
		$statement->execute();
		$statement->bind_result($db_id, $db_email, $db_fname, $db_lname, $db_disabled, $db_admin, $db_joined, $db_last_login);
		
		$responseArray = array();
		while($row = $statement->fetch()) {
			$rowArray = array("id"=>$db_id, "email"=>$db_email, "fname"=>$db_fname, "lname"=>$db_lname, "disabled"=>$db_disabled, "admin"=>$db_admin, "joined"=>$db_joined, "lastLogin"=>$db_last_login);
			array_push($responseArray, $rowArray);
		}
		
		$statement->close();
		return $responseArray;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getAllUsers", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

function getIPLogByUser($uid) {
	global $db;
	global $tbl_iplog;
	global $col_iplog_ip;
	global $col_iplog_date;
	global $col_iplog_user;
	
	global $API_GET_IPLOG_ID_NOT_RECEIVED;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "getIPLogByUser", "User not authorized to view IP Logs", "[".getUserEmailByID($_SESSION["id"])."]");
		return $ERROR_UNAUTHORIZED;
	}
	
	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_GET_IPLOG_ID_NOT_RECEIVED, "API", "getIPLogByUser", "Data not received", "User ID");
		return $API_GET_IPLOG_ID_NOT_RECEIVED;
	}
	
	$query = "SELECT $col_iplog_ip, $col_iplog_date FROM $tbl_iplog WHERE $col_iplog_user = ? ORDER BY $col_iplog_date";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->bind_result($db_ip, $db_date);
		
		$rowArray = array();
		while($row = $statement->fetch()) {
			$array = array("ip"=>$db_ip, "date"=>$db_date);
			array_push($rowArray, $array);
		}
		
		return $rowArray;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getIPLogByUser", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
	
}

function getUserEmailByID($uid) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_email;
	
	global $API_GET_USER_EMAIL_BY_ID_ID_NOT_RECEIVED;
	global $API_GET_USER_EMAIL_BY_ID_ACCOUNT_DOES_NOT_EXIST;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_GET_USER_EMAIL_BY_ID_ID_NOT_RECEIVED, "API", "getUserEmailByID", "Data not received", "User ID");
		return $API_GET_USER_EMAIL_BY_ID_ID_NOT_RECEIVED;
	}
	
	/*
	if(!isAdmin() && $uid != $_SESSION["id"]) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "getUserEmailByID", "User unauthorized to view other email addresses", getUserEmailByID($_SESION["id"]));
		return $ERROR_UNAUTHORIZED;
	}
	*/
	
	$query = "SELECT $col_user_email FROM $tbl_users WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->bind_result($db_email);
		
		if($statement->fetch() == null) {
			createLog("warning", $API_GET_USER_EMAIL_BY_ID_ACCOUNT_DOES_NOT_EXIST, "API", "getUserEmailByID", "No account exists", "[$uid]");
			$statement->close();
			return $API_GET_USER_EMAIL_BY_ID_ACCOUNT_DOES_NOT_EXIST;
		}
		
		$statement->close();
		return $db_email;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getUserEmailByID", "Failed to prepare query", $db->error." (".$db->errno.")");
		return "";
	}
}

function getUserIDByEmail($email) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_email;

	global $API_GET_USER_ID_BY_EMAIL_EMAIL_NOT_RECEIVED;
	global $API_GET_USER_ID_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST;
	global $ERROR_MYSQL;

	if(!isset($email) || $email == "") {
		createLog("warning", $API_GET_USER_ID_BY_EMAIL_EMAIL_NOT_RECEIVED, "API", "getUserIDByEmail", "Data not received", "Email address");
		return $API_GET_USER_ID_BY_EMAIL_EMAIL_NOT_RECEIVED;
	}

	// Not going to enforced logged in / admin at this point because this is called when trying to recover account
	$query = "SELECT $col_user_id FROM $tbl_users WHERE $col_user_email = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($db_user_id);
		if($statement->fetch() == null) {
			$statement->close();
			createLog("warning", $API_GET_USER_ID_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST, "API", "getUserIDByEmail", "Account does not exist for Email Address Provided", "[$email]");
			return $API_GET_USER_ID_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST;
		}
		$statement->close();
		return $db_user_id;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getUserIDByEmail", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

function getUserNameByEmail($email) {
	global $db;
	global $tbl_users;
	global $col_user_email;
	global $col_user_fname;
	global $col_user_lname;
	
	global $API_GET_USER_NAME_BY_EMAIL_EMAIL_ADDRESS_NOT_RECEIVED;
	global $API_GET_USER_NAME_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isset($email) | $email == "") {
		createLog("warning", $API_GET_USER_NAME_BY_EMAIL_EMAIL_ADDRESS_NOT_RECEIVED, "API", "getUserNameByEmail", "Data not received", "Email address");
		return array("fname"=>"", "lname"=>"");
	}
	
	// Don't check for admin as this is called when sharing a scenario
	// Called when not logged in when trying to recover the account
	/*if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "getUserNameByEmail", "User Not logged in", "-");
		//return $ERROR_UNAUTHORIZED;
		return array("fname"=>"", "lname"=>"");
	}*/
	
	$query = "SELECT $col_user_fname, $col_user_lname FROM $tbl_users WHERE $col_user_email = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($db_fname, $db_lname);
		
		if($statement->fetch() == null) {
			createLog("warning", $API_GET_USER_NAME_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST, "API", "getUserNameByEmail", "Account does not exist", "[$email]");
			$statement->close();
			return array("fname"=>"", "lname"=>"");
		}
		
		$statement->close();
		return array("fname"=>$db_fname, "lname"=>$db_lname);
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getUserNameByEmail", "Failed to prepare query", $db->error." (".$db->errno.")");
		return array("fname"=>"", "lname"=>"");
	}
}

/**
 * getUserName()
 *
 * @return String User's name
 */
function getUserNameByID($uid) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_fname;
	global $col_user_lname;
	
	global $API_GET_USER_NAME_BY_ID_ID_NOT_RECEIVED;
	global $API_GET_USER_NAME_BY_ID_ACCOUNT_DOES_NOT_EXIST;
	global $ERROR_MYSQL;
	
	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_GET_USER_NAME_BY_ID_ID_NOT_RECEIVED, "API", "getUserNameByID", "Data not received", "User ID");
		return array("fname"=>"", "lname"=>"");
	}
	
	// Only called when logged in, by any user for Navbar
	
	$query = "SELECT $col_user_fname, $col_user_lname FROM $tbl_users WHERE $col_user_id = ?";
	
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->bind_result($db_fname, $db_lname);
		if($statement->fetch() == null) {
			createLog("warning", $API_GET_USER_NAME_BY_ID_ACCOUNT_DOES_NOT_EXIST, "API", "getUserNameByID", "Account does not exist", "[$uid]");
		}
		$statement->close();
		return array("fname"=>$db_fname, "lname"=>$db_lname);
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getUserNameByID", "Failed to prepare query", $db->error." (".$db->errno.")");
		return array("fname"=>"", "lname"=>"");
	}
}
 
/**
 * getUserScenarios
 *
 * @param String $id User ID
 *
 * @return Array Array of scenarios ordered by date
 */
function getUserScenarios($id) {
	global $db;
	global $tbl_scenarios;
	global $col_scenario_id;
	global $col_scenario_user;
	global $col_scenario_name;
	global $col_scenario_created;
	
	global $API_GET_USER_SCENARIOS_USER_ID_NOT_RECEIVED;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	
	if(!isset($id) || $id == "") {
		createLog("warning", $API_GET_USER_SCENARIOS_USER_ID_NOT_RECEIVED, "API", "getUserScenarios", "Data not received", "User ID");
		return $API_GET_USER_SCENARIOS_USER_ID_NOT_RECEIVED;
	}
	
	if(!isAdmin() && $id != $_SESSION["id"]) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "getUserScenarios", "User not authorized to view all of other user's scenarios", getUserNameByID($_SESSION["id"]));
		return $ERROR_UNAUTHORIZED;
	}
	
	$query = "SELECT $col_scenario_id, $col_scenario_name, $col_scenario_created FROM $tbl_scenarios WHERE $col_scenario_user = ? ORDER BY $col_scenario_created DESC";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $id);
		$statement->execute();
		$statement->bind_result($db_id, $db_name, $db_date);
		
		$response_array = array();
		
		while($row = $statement->fetch()) {
			$row_array = array("id"=>$db_id, "name"=>$db_name, "date"=>$db_date);
			array_push($response_array, $row_array);
		}
		
		$statement->close();
		return $response_array;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "getUserScenarios", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

function grantAdmin($uid) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_admin;
	
	global $ERROR_MYSQL;
	global $ERROR_UNAUTHORIZED;
	global $API_GRANT_ADMIN_ID_NOT_RECEIVED;
	global $API_GRANT_ADMIN_ADMIN_GRANTED;
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "grantAdmin", "User not authorized to grant admin privileges", "[".getUserEmailByID($_SESSION["id"])."]");
		return $ERROR_UNAUTHORIZED;
	}
	
	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_GRANT_ADMIN_ID_NOT_RECEIVED, "API", "grantAdmin", "Data not received", "User ID");
		return $API_GRANT_ADMIN_ID_NOT_RECEIVED;
	}
	
	$query = "UPDATE $tbl_users SET $col_user_admin = 1 WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->close();
		
		createLog("success", $API_GRANT_ADMIN_ADMIN_GRANTED, "API", "grantAdmin", "Admin privileges granted", "[".getUserEmailByID($uid)."]");
		return $API_GRANT_ADMIN_ADMIN_GRANTED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "grantAdmin", "Failed to prepare query", $db->error." (".$db_errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * isAdmin()
 *
 * @return boolean Indicates if the user is an administrator
 */
function isAdmin() {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_admin;
	
	global $ERROR_MYSQL;
	
	$query = "SELECT $col_user_admin FROM $tbl_users WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $_SESSION["id"]);
		$statement->execute();
		$statement->bind_result($db_admin);
		$statement->fetch();
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "isAdmin", "Failed to prepare query", $db->error." (".$db->errno.")");
		return false;
	}
	
	return ($db_admin == "1");
}
 
/**
 * isLoggedIn()
 *
 * @return boolean Indicates if the user is logged into the system
 *
 * Checks to see if SESSION["id"] is set, then ensures that the ID is valid.
 */
function isLoggedIn() {
	global $db;
	global $tbl_users;
	global $col_user_id;	
	
	global $API_IS_LOGGED_IN_SESSION_ID_INVALID;
	global $ERROR_MYSQL;
	
	if(!isset($_SESSION["id"]) || $_SESSION["id"] == "") {
		return false;
	}
	
	$query = "SELECT $col_user_id FROM $tbl_users WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $_SESSION["id"]);
		$statement->execute();
		$statement->bind_result($db_id);
		if($statement->fetch() == null) {
			createLog("danger", $API_IS_LOGGED_IN_SESSION_ID_INVALID, "API", "isLoggedIn", "Invalid SESSION ID", "[".$_SESSION["id"]."]");
			$statement->close();
			logout();
			return false;
		} else {
			$statement->close();
			return true;
		}
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "isLoggedIn", "Failed to prepare query", $db->error." (".$db->errno.")");
		return false;
	}
}

/**
 * login
 * 
 * @param String $email User account's email address
 * @param String $password User account's password
 *
 * @return Integer Response code according to any error that occurred or success of function.
 *
 * Receive data for a user's account and attempt to log into the account.
 */
function login($email, $password) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_email;
	global $col_user_password;
	global $col_user_login_attempts;
	global $col_user_disabled;
	global $col_user_last_login;

	global $tbl_iplog;
	global $col_iplog_id;
	global $col_iplog_ip;
	global $col_iplog_date;
	global $col_iplog_user;
	
	global $API_LOGIN_EMAIL_NOT_RECEIVED;
	global $API_LOGIN_PASSWORD_NOT_RECEIVED;
	global $API_LOGIN_ACCOUNT_DOES_NOT_EXIST;
	global $API_LOGIN_ACCOUNT_DISABLED;
	global $API_LOGIN_USER_AUTHENTICATED;
	global $API_LOGIN_INCORRECT_PASSWORD;
	global $API_LOGIN_DISABLING_ACCOUNT;
	
	global $ERROR_MYSQL;
	
	// Ensure that data is received
	if(!isset($email) || $email == "") {
		createLog("warning", $API_LOGIN_EMAIL_NOT_RECEIVED, "API", "login", "Data not received", "Email address");
		return $API_LOGIN_EMAIL_NOT_RECEIVED;
	}
	
	if(!isset($password) || $password == "") {
		createLog("warning", $API_LOGIN_PASSWORD_NOT_RECEIVED, "API", "login", "Data not received", "Password");
		return $API_LOGIN_PASSWORD_NOT_RECEIVED;
	}
	
	// Get user's information
	$query = "SELECT $col_user_id, $col_user_email, $col_user_password, $col_user_login_attempts, $col_user_disabled FROM $tbl_users WHERE $col_user_email = ?";
	
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($db_id, $db_email, $db_password, $db_loginAttempts, $db_userDisabled);
		
		if($statement->fetch() == null) {
			createLog("warning", $API_LOGIN_ACCOUNT_DOES_NOT_EXIST, "API", "login", "Account does not exists", "[$email]");
			$statement->close();
			return $API_LOGIN_ACCOUNT_DOES_NOT_EXIST;
		}
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
	
	if($db_userDisabled == 1) {
		createLog("warning", $API_LOGIN_ACCOUNT_DISABLED, "API", "login", "Account currently disabled", "[$email]");
		return $API_LOGIN_ACCOUNT_DISABLED;
	}
	
	// Verify the password
	if(password_verify($password, $db_password)) {		
		// Set SESSION variables
		$_SESSION["id"] = $db_id;
		
		// Reset login attempts to 0
		$query = "UPDATE $tbl_users SET $col_user_login_attempts = 0 WHERE $col_user_id = ?";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("s", $db_id);
			$statement->execute();
			$statement->close();
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
			// Do not return anything
		}
		
		// Log the user's ip in database only if it's a new IP address
		$query = "SELECT $col_iplog_ip FROM $tbl_iplog WHERE $col_iplog_user = ?";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("s", $db_id);
			$statement->execute();
			$statement->bind_result($db_ip);
			
			$newIP = true;
			while($row = $statement->fetch()) {
				if($db_ip == $_SERVER["REMOTE_ADDR"]) {
					$newIP = false;
					break;
				}
			}
			$statement->close();
			
			if($newIP) {
				$query = "INSERT INTO $tbl_iplog ($col_iplog_ip, $col_iplog_user) VALUES (?, ?)";
				if($statement = $db->prepare($query)) {
					$statement->bind_param("ss", $_SERVER["REMOTE_ADDR"], $db_id);
					$statement->execute();
					$statement->close();
				} else {
					createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
				}
			}
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
		}
		
		// Update the last login time for the user
		$loginDate = date("Y-m-d H:i:s");
		$query = "UPDATE $tbl_users SET $col_user_last_login = ? WHERE $col_user_id = ?";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("ss", $loginDate, $db_id);
			$statement->execute();
			$statement->close();
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
		}
		
		createLog("success", $API_LOGIN_USER_AUTHENTICATED, "API", "login", "User logged in", "[$email]");
		return $API_LOGIN_USER_AUTHENTICATED;
	} else {
		createLog("warning", $API_LOGIN_INCORRECT_PASSWORD, "API", "login", "Incorrect password", "[$email]");
		
		$db_loginAttempts++;
		
		// Update the table
		$query = "UPDATE $tbl_users SET $col_user_login_attempts = ? WHERE $col_user_id = ?";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("is", $db_loginAttempts, $db_id);
			$statement->execute();
			$statement->close();
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
			return $ERROR_MYSQL;
		}
		
		if($db_loginAttempts > 5) {
			createLog("danger", $API_LOGIN_DISABLING_ACCOUNT, "API", "login", "Disabling account due to over 5 failed login attempts", "[$email]");
			
			$query = "UPDATE $tbl_users SET $col_user_disabled = 1 WHERE $col_user_id = ?";
			if($statement = $db->prepare($query)) {
				$statement->bind_param("s", $db_id);
				$statement->execute();
				$statement->close();
				return $API_LOGIN_DISABLING_ACCOUNT;
			} else {
				createLog("danger", $ERROR_MYSQL, "API", "login", "Failed to prepare query", $db->error." (".$db->errno.")");
				return $ERROR_MYSQL;
			}
		}
		return $API_LOGIN_INCORRECT_PASSWORD;
	}
}

/**
 * logout
 *
 * Destroy SESSION and log user out
 */
function logout() {
	global $API_LOGOUT_USER_LOGGED_OUT;
	
	createLog("success", $API_LOGOUT_USER_LOGGED_OUT, "API", "logout", "User logged out", "[".getUserEmailByID($_SESSION["id"])."]");
	
	foreach($_SESSION as $key=>$data) {
		unset($_SESSION[$key]);
	}
	
	session_destroy();
}

function recoverAccount($email) {
	global $db;
	global $tbl_users;
	global $tbl_reset;
	global $col_user_email;
	global $col_reset_id;
	global $col_reset_email;
	global $col_reset_token;

	global $API_RECOVER_ACCOUNT_ACCOUNT_DOES_NOT_EXIST;
	global $API_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED;
	global $ERROR_MYSQL;

	if(!isset($email) || $email == "") {
		createLog("warning", $API_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED, "API", "recoverAccount", "Data not received", "Email address");
		return $API_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED;
	}

	// First, check to see if the email is in the system
	$query = "SELECT $col_user_email FROM $tbl_users WHERE $col_user_email = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($db_email);
		if($statement->fetch() == null) {
			createLog("warning", $API_RECOVER_ACCOUNT_ACCOUNT_DOES_NOT_EXIST, "API", "recoverAccount", "Account Does Not Exist", "[$email]");
			return $API_RECOVER_ACCOUNT_ACCOUNT_DOES_NOT_EXIST;
		}
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "recoverAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}

	// Create a token for the database
	$token = createKey();
	$tokenID = createKey();

	// Check to see if a reset password token already exists
	$tokenExists = false;
	$query = "SELECT $col_reset_id FROM $tbl_reset WHERE $col_reset_email = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $email);
		$statement->execute();
		$statement->bind_result($db_reset_id);
		if($statement->fetch() == null) {
			$tokenExists = false;
		} else {
			$tokenExists = true;
		}
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "recoverAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}

	// Update or insert the token
	// No need to update the created column becuase the SQL is set up to CURRENT_TIMESTAMP on UPDATE and the default value is CURRENT_TIMESTAMP 
	if($tokenExists) {
		$query = "UPDATE $tbl_reset SET $col_reset_token = ? WHERE $col_reset_email = ?";
	} else {
		$query = "INSERT INTO $tbl_reset ($col_reset_id, $col_reset_email, $col_reset_token) VALUES (?, ?, ?)"; 
	}
	if($statement = $db->prepare($query)) {
		if($tokenExists) {
			$statement->bind_param("ss", $token, $email);
		} else {
			$statement->bind_param("sss", $tokenID, $email, $token);
		}
		$statement->execute();
		$statement->close();
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "recoverAccount", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}

	// Email is going to be sent from the recover-account-do file
	return $token;
}

function revokeAdmin($uid) {
	global $db;
	global $tbl_users;
	global $col_user_id;
	global $col_user_admin;
	
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;
	global $API_REVOKE_ADMIN_ID_NOT_RECEIVED;
	global $API_REVOKE_ADMIN_ADMIN_REVOKED;
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "API", "revokeAdmin", "User not authorized to revoke admin privileges", "[".getUserEmailByID($_SESSION["id"])."]");
		return $ERROR_UNAUTHORIZED;
	}
	
	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_REVOKE_ADMIN_ID_NOT_RECEIVED, "API", "revokeAdmin", "Data not received", "User ID");
		return $API_REVOKE_ADMIN_ID_NOT_RECEIVED;
	}
	
	$query = "UPDATE $tbl_users SET $col_user_admin = 0 WHERE $col_user_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("s", $uid);
		$statement->execute();
		$statement->close();
		
		createLog("success", $API_REVOKE_ADMIN_ADMIN_REVOKED, "API", "revokeAdmin", "Admin privilges revoked", "[".getUserEmailByID($uid)."]");
		return $API_REVOKE_ADMIN_ADMIN_REVOKED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "revokeAdmin", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * saveScenario
 *
 * @param String $id User ID
 * @param String $name Scenario Name
 * @param String $data Scenario Data
 *
 * @return Integer Status code
 */
function saveScenario($id, $name, $data) {
	global $db;
	global $tbl_scenarios;
	global $col_scenario_id;
	global $col_scenario_name;
	global $col_scenario_user;
	global $col_scenario_created;
	global $col_scenario_data;
	
	global $API_SAVE_SCENARIO_USER_ID_NOT_RECEIVED;
	global $API_SAVE_SCENARIO_DATA_NOT_RECEVED;
	global $API_SAVE_SCENARIO_SCENARIO_SAVED;
	global $ERROR_MYSQL;
	
	$date = date("Y-m-d H:i:s");
	
	if(!isset($id) || $id == "") {
		createLog("warning", $API_SAVE_SCENARIO_USER_ID_NOT_RECEIVED, "API", "saveScenario", "Data not received", "User ID");
		return $API_SAVE_SCENARIO_USER_ID_NOT_RECEIVED;
	}
	
	if(!isset($name) || $name == "") {
		createLog("info", $API_SAVE_SCENARIO_NAME_NOT_RECEIVED, "API", "saveScenario", "Data not received", "Scenario Name");
		$name = "Scenario ".$date;		
	}
	
	if(!isset($data) || $data == "") {
		createLog("warning", $API_SAVE_SCENARIO_DATA_NOT_RECEIVED, "API", "saveScenario", "Data not received", "Mission data");
		return $API_SAVE_SCENARIO_DATA_NOT_RECEVIED;
	}
	
	$key = createKey();
	
	$query = "INSERT INTO $tbl_scenarios ($col_scenario_id, $col_scenario_name, $col_scenario_user, $col_scenario_created, $col_scenario_data) VALUES (?, ?, ?, ?, ?)";
	
	if($statement = $db->prepare($query)) {
		$statement->bind_param("sssss", $key, $name, $id, $date, $data);
		if($statement->execute()) {
			$statement->close();
			createLog("success", $API_SAVE_SCENARIO_SCENARIO_SAVED, "API", "saveScenario", "Scenario Saved", "-");
			return $API_SAVE_SCENARIO_SCENARIO_SAVED;
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "SaveScenario", "Failed to execute query", $db->error." (".$db->errno.")");
			$statement->close();
			return $ERROR_MYSQL;
		}
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "SaveScenario", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

/**
 * updateScenario
 *
 * @param String $uid User ID
 * @param String $sid Scenario ID
 * @param String $name Scenario Name
 * @param String $data Scenario Data
 */
function updateScenario($uid, $sid, $name, $data) {
	global $db;
	global $tbl_scenarios;
	global $col_scenario_id;
	global $col_scenario_name;
	//global $col_scenario_user;
	global $col_scenario_created;
	global $col_scenario_data;

	global $API_UPDATE_SCENARIO_USER_ID_NOT_RECEIVED;
	global $API_UPDATE_SCENARIO_SCENARIO_ID_NOT_RECEIVED;
	global $API_UPDATE_SCENARIO_NAME_NOT_RECEIVED;
	global $API_UPDATE_SCENARIO_DATA_NOT_RECEIVED;
	global $API_UPDATE_SCENARIO_SCENARIO_UPDATED;
	global $ERROR_UNAUTHORIZED;
	global $ERROR_MYSQL;

	$date = date("Y-m-d H:i:s");

	if(!isset($uid) || $uid == "") {
		createLog("warning", $API_UPDATE_SCENARIO_USER_ID_NOT_RECEIVED, "API", "updateScenario", "Data not received", "User ID");
		return $API_UPDATE_SCENARIO_USER_ID_NOT_RECEIVED;
	}

	if(!isset($sid) || $sid == "") {
		createLog("warning", $API_UPDATE_SCENARIO_SCENARIO_ID_NOT_RECEIVED, "API", "updateScenario", "Data not received", "Scenario ID");
		return $API_UPDATE_SCENARIO_SCENARIO_ID_NOT_RECEIVED;
	}

	if(!isset($name) || $name == "") {
		createLog("info", $API_UPDATE_SCENARIO_NAME_NOT_RECEIVED, "API", "updateScenario", "Data not received", "Scenario Name");
		$name = "Scenario ".$date;
	}

	if(!isset($data) || $data == "") {
		createLog("warning", $API_UPDATE_SCENARIO_DATA_NOT_RECEIVED, "API", "updateScenario", "Data not received", "Mission data");
		return $API_UPDATE_SCENARIO_DATA_NOT_RECEIVED;
	}

	// Only the owner can overwrite the scenario, not even an admin can do this
	if($uid != $_SESSION["id"]) {
		createLog("danger", $ERROR_UNAUTHORIZED, "API", "updateScenario", "User not authorized to update scenario", getUserEmailByID($_SESSION["id"]));
		return $ERROR_UNAUTHORIZED;
	}

	$query = "UPDATE $tbl_scenarios SET $col_scenario_name = ?, $col_scenario_created = ?, $col_scenario_data = ? WHERE $col_scenario_id = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ssss", $name, $date, $data, $sid);
		$statement->execute();
		$statement->close();

		return $API_UPDATE_SCENARIO_SCENARIO_UPDATED;
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "updateScenario", "Failed to prepare query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}
}

function validateRecoveryToken($email, $token) {
	global $db;
	global $tbl_reset;
	global $col_reset_id;
	global $col_reset_email;
	global $col_reset_token;
	global $col_reset_created;

	global $ERROR_MYSQL;
	global $API_VALIDATE_RECOVERY_TOKEN_EMAIL_NOT_RECEIVED;
	global $API_VALIDATE_RECOVERY_TOKEN_TOKEN_NOT_RECEIVED;
	global $API_VALIDATE_RECOVERY_TOKEN_COMBINATION_DOES_NOT_EXIST;
	global $API_VALIDATE_RECOVERY_TOKEN_TOKEN_EXPIRED;
	global $API_VALIDATE_RECOVERY_TOKEN_TOKEN_VALID;

	if(!isset($email) || $email == "") {
		createLog("warning", $API_VALIDATE_RECOVERY_TOKEN_EMAIL_NOT_RECEIVED, "API", "validateRecoveryToken", "Data not received", "Email address");
		return $API_VALIDATE_RECOVERY_TOKEN_EMAIL_NOT_RECEIVED;
	}

	if(!isset($token) || $token == "") {
		createLog("warning", $API_VALIDATE_RECOVERY_TOKEN_TOKEN_NOT_RECEIVED, "API", "validateRecoveryToken", "Data not received", "Token");
		return $API_VALIDATE_RECOVERY_TOKEN_TOKEN_NOT_RECEIVED;
	}

	// Select the ID and the creation date for the token
	// I don't need to reselect the email and the token
	$query = "SELECT $col_reset_id, $col_reset_created FROM $tbl_reset WHERE $col_reset_email = ? AND $col_reset_token = ?";
	if($statement = $db->prepare($query)) {
		$statement->bind_param("ss", $email, $token);
		$statement->execute();
		$statement->bind_result($db_reset_id, $db_reset_created);
		if($statement->fetch() == null) {
			$statement->close();
			createLog("warning", $API_VALIDATE_RECOVERY_TOKEN_COMBINATION_DOES_NOT_EXIST, "API", "validateRecoveryToken", "Invalid token combination", "[$email] // [$token]");
			return $API_VALIDATE_RECOVERY_TOKEN_COMBINATION_DOES_NOT_EXIST;
		} else {
			$statement->close();
		}
	} else {
		createLog("danger", $ERROR_MYSQL, "API", "validateRecoveryToken", "Failed to prepare the query", $db->error." (".$db->errno.")");
		return $ERROR_MYSQL;
	}

	// The combination of the email address and the token are correct, so now we check that it is still valid
	$current_time = time();
	$check_time = strtotime("+30 minutes", strtotime($db_reset_created));

	// If Current time is > 30 minutes beyond the created time
	if ($current_time > $check_time) {
		// Remove the database entry
		$query = "DELETE FROM $tbl_reset WHERE $col_reset_id = ?";
		if($statement = $db->prepare($query)) {
			$statement->bind_param("s", $db_reset_id);
			$statement->execute();
			$statement->close();

			createLog("warning", $API_VALIDATE_RECOVERY_TOKEN_TOKEN_EXPIRED, "API", "validateRecoveryToken", "Token Expired", "[$email]");
			return $API_VALIDATE_RECOVERY_TOKEN_TOKEN_EXPIRED;
		} else {
			createLog("danger", $ERROR_MYSQL, "API", "validateRecoveryToken", "Failed to prepare query", $db->error." (".$db->errno.")");
			return $ERROR_MYSQL;			
		}
	} else {
		return $API_VALIDATE_RECOVERY_TOKEN_TOKEN_VALID;
	}
}

/**
 * getLogStatus()
 * 
 * Params: null
 * 
 * Return (Success): $log_level_status (Array): Associative array ["level"] => Boolean
 * Return (Failure): null
 * 
 * Queries the database to retrieve saved site settings for site logging.
 * The array is associative where the index is the level and the value is true or false.
 * If there is an error or failure to complete the query, returns null.
 */
/*function getLogStatus() {
	log_apimsg("Getting log enabled statuses.");
	
	// Get database variables
	global $db;
	global $odt_log;
	global $odt_log_level;
	global $odt_log_enabled;
	
	// Get status codes
	
	
	// Get the status of which logs are enabled
	$log_level_status = array();
	$query = "SELECT $odt_log_level, $odt_log_enabled FROM $odt_log";
	
	log_debugmsg("Query: $query");
	
	if($statement = $db->prepare($query)) {
		$statement->execute();
		$statement->bind_result($db_log_level, $db_log_enabled);

		while($row = $statement->fetch()) {
			$log_level_status[$db_log_level] = $db_log_enabled;
		}
		$statement->close();
		
		log_apimsg("Returning log enabled statuses.");
		return $log_level_status;
	} else {
		log_errormsg("There was an error preparing the query \"$query\".");
		log_errormsg("Could not retrieve log enabled status.");
		log_errormsg("MySQL error: ".$db->error." (".$db->errno.")");
		return null;
	}
}*/
?>