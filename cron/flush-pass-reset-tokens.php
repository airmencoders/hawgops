<?php
	// Run by CRON job that is run every hour
	// 0 * * * * /usr/bin/php /var/www/html/cron/flush-pass-reset-tokens.php > /opt/bitnami/apache2/htdocs/cron/logs/flush-pass-reset-tokens-log.txt
	// I'm okay with this being every hour, even though that the reset tokens are only valid for 30 minutes, becuase the recover-account page will check the date-time group associated with the token in the database, and if that's bad then it will not work. This is just to help keep the number of statle tokens to a minimum.

	//require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	//require("../req/keys/recaptcha.php");
	//require("../req/all/api-v1.php");	

	$currentTime = time();

	// Select all the reset pass tokens
	$query = "SELECT * FROM $tbl_reset";
	if($statement = $db->prepare($query)) {
		$statement->execute();

		while($row = $statement->fetch_assoc()) {
			$checkTime = strtotime("+30 minutes", strtotime($row["created"]));

			if($currentTime > $checkTime) {
				$deleteQuery = "DELETE FROM $tbl_reset WHERE $col_reset_id = ?";
				if($deleteStatement = $db->prepare($deleteQuery)) {
					$deleteStatement->bind_param("s", $row["id"]);
					$deleteStatement->execute();
					$deleteStatement->close();
					echo "Deleted Token: Email: ".$row["email"]." // Token: ".$row["token"]." // ID: ".$row["id"]." // Created: ".$row["created"]."\n";
				} else {
					echo "Failed to prepare query: ".$db->error." (".$db->errno.")\n";
					exit();
				}
			}
		}
		$statement->close();
	} else {
		echo "Failed to prepare query: ".$db->error." (".$db->errno.")\n";
		exit();
	}
?>