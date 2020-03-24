<?php
	// Run by CRON job that is run every hour
	// 0 * * * * /opt/bitnami/php /opt/bitnami/apache2/htdocs/cron/flush-pass-reset-tokens.php > /dev/null 2>&1
	// I'm okay with this being every hour, even though that the reset tokens are only valid for 30 minutes, becuase the recover-account page will check the date-time group associated with the token in the database, and if that's bad then it will not work. This is just to help keep the number of statle tokens to a minimum.

	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	//require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");	

	$currentTime = time();

	// Select all the reset pass tokens
	$query = "SELECT * FROM $tbl_reset";
	if($statement = $db->prepare($query)) {
		$statement->execute();
		$result = $statement->get_result();
		$statement->close();

		while($row = $result->fetch_assoc()) {
			$checkTime = strtotime("+30 minutes", strtotime($row["created"]));

			if($currentTime > $checkTime) {
				$deleteQuery = "DELETE FROM $tbl_reset WHERE $col_reset_id = ?";
				if($deleteStatement = $db->prepare($deleteQuery)) {
					$deleteStatement->bind_param("s", $row["id"]);
					$deleteStatement->execute();
					$deleteStatement->close();

					createLog("secondary", "-", "CRON", "flushPassResetTokens", "Token Deleted", "Email: ".$row["email"]." // Created: ".$row["created"]);
				} else {
					createLog("danger", $ERROR_MYSQL, "CRON", "flushPassResetTokens", "Failed to prepare query", $db->error." (".$db->errno.")");
					closeLogs();
				}
			}
		}
	} else {
		createLog("danger", $ERROR_MYSQL, "CRON", "flushPassResetTokens", "Failed to prepare query", $db->error." (".$db->errno.")");
		closeLogs();
	}
?>