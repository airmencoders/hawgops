<?php
	if(isLoggedIn()) {
		$name = getUserNameByID($_SESSION["id"]);
	}
?>

<div id="class-banner" class="bg-success text-center">
	<div class="text-center font-weight-bolder">
		// UNCLASSIFIED //
	</div>
</div>
<nav class="navbar navbar-expand-xl navbar-dark bg-dark fixed-top">
	<a class="navbar-brand" href="/">Hawg Ops</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle Navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse justify-content-between" id="navbar">
		<ul class="navbar-nav">
			<!--<li class="nav-item">
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/wx.php") ? "active" : ""; ?>" href="/wx">WX</a>
			</li>-->
			<li class="nav-item">	
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/cas.php") ? "active" : ""; ?>" href="/cas">CAS Planner</a>
			</li>
			<li class="nav-item">
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/airspace-kml.php") ? "active" : ""; ?>" href="/airspace-kml">Airspace KML</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://hawg-ops.com">Version 2</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://badbirdbook.com" target="_blank" rel="noopener noreferrer">A-10 Bad Bird Book <i class="fas fa-external-link-alt"></i></a>
			</li>
		</ul>
		<ul class="navbar-nav">
			<!--<li class="nav-item" id="notify-online">
				<a class="nav-link" href="#"><i class="fas fa-check-circle text-success"></i> Online</a>
			</li>
			<li class="nav-item d-none" id="notify-offline">
				<a class="nav-link" href="#"><i class="fas fa-exclamation-circle text-danger"></i> Offline</a>
			</li>
			</li>-->
			<?php if(isLoggedIn()) { ?>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="account-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-user-circle"></i> <?php echo $name["fname"]." ".$name["lname"]; ?>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="account-toggle">
					<?php if(isAdmin()) { ?>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/my-account.php") ? "active" : ""; ?>" href="/my-account">My Account</a>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/admin-users.php") ? "active" : ""; ?>" href="/admin-users">Users</a>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/admin-logs.php") ? "active" : ""; ?>" href="/admin-logs">Logs</a>
					<div class="dropdown-divider"></div>
					<?php } ?>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/my-scenarios.php") ? "active" : ""; ?>" href="/my-scenarios">My Scenarios</a>
					<!--<a class="dropdown-item" href="/account-settings">Account Settings</a>-->
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/logout">Logout</a>
				</div>
			</li>
			<?php } else { ?>
			<li class="nav-item">
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/login.php") ? "active" : ""; ?>" href="/login">Login</a>
			</li>
			<?php } ?>
			<li class="nav-item">
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/contact.php") ? "active" : ""; ?>" href="/talk-to-me">Contact Porkins</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="policy-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					About
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="policy-toggle">
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/terms.php") ? "active" : ""; ?>" href="./terms" target="_blank">Terms of Use</a>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/privacy.php") ? "active" : ""; ?>" href="./privacy" target="_blank">Privacy Policy</a>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/cookies.php") ? "active" : ""; ?>" href="./cookies" target="_blank">Cookie Policy</a>
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/licenses.php") ? "active" : ""; ?>" href="./licenses" target="_blank">Licenses</a>
					<div class="dropdown-divider"></div>
					<div class="dropdown-item">v<?php require("../VERSION.md"); ?></div>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://github.com/airmencoders/hawgops" target="_blank"><i class="fab fa-github"></i></a>
			</li>
		</ul>
	</div>
</nav>