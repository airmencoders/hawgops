<?php
	// Run by CRON job that is run every hour
	// 0 0 * * * /opt/bitnami/php /opt/bitnami/apache2/htdocs/cron/inactive-users.php > /dev/null 2>&1

	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/smtp.php");
	//require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");

	$crlf = "\r\n";
	$from = "hawg.ops@gmail.com";

	$currentTime = time();

	// Select all the reset pass tokens

	$query = "SELECT $col_user_id, $col_user_fname, $col_user_email, $col_user_joined, $col_user_last_login FROM $tbl_users";
	if($statement = $db->prepare($query)) {
		$statement->execute();
		$result = $statement->get_result();
		$statement->close();

		while($row = $result->fetch_assoc()) {

			// If the user has no date of last login (haven't logged in since implemented), then just use the date that the account was created
			if($row["lastLogin"] == null) {
				$months12 = strtotime("+12 months", strtotime($row["joined"]));
				$months14 = strtotime("+14 months", strtotime($row["joined"]));
				$months15 = strtotime("+15 months", strtotime($row["joined"]));
			} else {
				$months12 = strtotime("+12 months", strtotime($row["lastLogin"]));
				$months14 = strtotime("+14 months", strtotime($row["lastLogin"]));
				$months15 = strtotime("+15 months", strtotime($row["lastLogin"]));
			}

			if($currentTime > $months15) {
				// Download and remove all the scenarios
				$allUserScenarios = array();
				$scenarioQuery = "SELECT $col_scenario_name, $col_scenario_data FROM $tbl_scenarios WHERE $col_scenario_user = ?";

				if($scenarioStatement = $db->prepare($scenarioQuery)) {
					$scenarioStatement->bind_param("s", $row["id"]);
					$scenarioStatement->execute();
					$scenarioResult = $scenarioStatement->get_result();
					$scenarioStatement->close();

					while($scenarioRow = $scenarioResult->fetch_assoc()) {
						$tempArray = array($scenarioRow["name"] => $scenarioRow["data"]);
						array_push($allUserScenarios, $tempArray);
					}
					
					createLog("secondary", "-", "CRON", "inactiveUsers", "Downloaded user's scenarios", "ID: [".$row["id"]."]");
				} else {
					createLog("danger", $ERROR_MYSQL, "CRON", "inactiveUsers", "Failed to prepare query", $db->error." (".$db->errno.")");
					closeLogs();
				}

				// Delete the scenarios
				$scenarioQuery = "DELETE FROM $tbl_scenarios WHERE $col_scenario_user = ?";
				if($scenarioStatement = $db->prepare($scenarioQuery)) {
					echo "deleting scenarios"."<br/>";
					$scenarioStatement->bind_param("s", $row["id"]);
					$scenarioStatement->execute();
					$scenarioStatement->close();
				} else {
					createLog("danger", $ERROR_MYSQL, "CRON", "inactiveUsers", "Failed to prepare query", $db->error." (".$db->errno.")");
					closeLogs();
				}

				// Delete the account
				$deleteQuery = "DELETE FROM $tbl_users WHERE $col_user_id = ?";
				if($deleteStatement = $db->prepare($deleteQuery)) {
					echo "deleting account"."<br/>";
					$deleteStatement->bind_param("s", $row["id"]);
					$deleteStatement->execute();
					$deleteStatement->close();

					createLog("secondary", "-", "CRON", "inactiveUsers", "Deleted User Account", "Email: ".$row["email"]);
				}

				// Format the scenario Data
				$fScenarioData = "";
				foreach($allUserScenarios as $name => $data) {
					$fScenarioData .= $name."<br/>";
					$fScenarioData .= $data."<br/><br/>";
				}

				echo "formatted data: ".$fScenarioData."<br/>";

				// Email the user
				$to = $row["email"];
				$full_message = file_get_contents("../req/emails/account-deleted-template.php");
				$full_message = str_replace("__USERNAME__", $row["fname"], $full_message);
				$full_message = str_replace("__SCENARIODATA__", $fScenarioData, $full_message);

				$headers = array("From" => $from, "To" => $row["email"], "Subject" => "Hawg Ops | Account Deleted", "Content-Type" => "text/html; charset=UTF-8");

				$mime = new Mail_mime(array("eol" => $crlf, "text-charset" => "UTF-8", "html_charset" => "UTF-8", "head_charset" => "UTF-8"));

				$mime->setHTMLBody($full_message);

				$body = $mime->get();
				$headers = $mime->headers($headers);

				$smtp = Mail::factory("smtp",
					array("host" => $host,
						"port" => $port,
						"auth" => true,
						"username" => $gUser,
						"password" => $gPass));

				$mail = $smtp->send($row["email"], $headers, $body);
				closeLogs();
			} else if($currentTime > $months14) {
				// Disable the account
				$disableQuery = "UPDATE $tbl_users SET $col_user_disabled = 1 WHERE $col_user_id = ?";
				if($disableStatement = $db->prepare($disableQuery)) {
					$disableStatement->bind_param("s", $row["id"]);
					$disableStatement->execute();
					$disableStatement->close();
				} else {
					createLog("danger", $ERROR_MYSQL, "CRON", "inactiveUsers", "Failed to prepare query", $db->error." (".$db->errno.")");
					closeLogs();
				}

				// Email the user
				$to = $row["email"];
				$full_message = file_get_contents("../req/emails/account-disabled-template.php");
				$full_message = str_replace("__USERNAME__", $row["fname"], $full_message);
				$full_message = str_replace("__USEREMAIL__", $row["email"], $full_message);

				$headers = array("From" => $from, "To" => $row["email"], "Subject" => "Hawg Ops | Account Disabled", "Content-Type" => "text/html; charset=UTF-8");

				$mime = new Mail_mime(array("eol" => $crlf, "text-charset" => "UTF-8", "html_charset" => "UTF-8", "head_charset" => "UTF-8"));

				$mime->setHTMLBody($full_message);

				$body = $mime->get();
				$headers = $mime->headers($headers);

				$smtp = Mail::factory("smtp",
					array("host" => $host,
						"port" => $port,
						"auth" => true,
						"username" => $gUser,
						"password" => $gPass));

				$mail = $smtp->send($row["email"], $headers, $body);
				closeLogs();
				
			} else if($currentTime > $months12) {
				// Email the user
				$to = $row["email"];
				$full_message = file_get_contents("../req/emails/account-inactive-template.php");
				$full_message = str_replace("__USERNAME__", $row["fname"], $full_message);
				$full_message = str_replace("__USEREMAIL__", $row["email"], $full_message);

				$headers = array("From" => $from, "To" => $row["email"], "Subject" => "Hawg Ops | Account Inactive", "Content-Type" => "text/html; charset=UTF-8");

				$mime = new Mail_mime(array("eol" => $crlf, "text-charset" => "UTF-8", "html_charset" => "UTF-8", "head_charset" => "UTF-8"));

				$mime->setHTMLBody($full_message);

				$body = $mime->get();
				$headers = $mime->headers($headers);

				$smtp = Mail::factory("smtp",
					array("host" => $host,
						"port" => $port,
						"auth" => true,
						"username" => $gUser,
						"password" => $gPass));

				$mail = $smtp->send($row["email"], $headers, $body);
				closeLogs();
			}
		}
	} else {
		createLog("danger", $ERROR_MYSQL, "CRON", "inactiveUsers", "Failed to prepare query", $db->error." (".$db->errno.")");
		closeLogs();
	}
?>