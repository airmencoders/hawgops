<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php require("../req/head/head.php"); ?>
		<script src="./js/validation.js"></script>
	</head>
	<body id="bg">
		<?php require("../req/structure/navbar.php")?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			
			<div class="card" style="margin-top: 2rem;">
				<h4 class="card-header text-center">
					Contact Porkins
				</h4>
				<form method="POST" action="./do/contact-do.php" onsubmit="return validateContactForm()">
					<div class="card-body">
						<div class="form-group">
							<label for="name">Your Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="user-name" name="user-name">
							<div class="invalid-feedback">Name is required.</div>
						</div>
						<div class="form-group">
							<label for="email">Your Email Address<span class="text-danger">*</span></label>	
							<input type="email" class="form-control" id="user-email" name="user-email">
							<div class="invalid-feedback">Email address is required.</div>
						</div>
						<div class="form-group">
							<label for="subject">Subject</label>
							<input type="text" class="form-control" id="subject" name="subject" placeholder="Hawg Ops Feedback">
						</div>
						<div class="form-group">
							<label for="message">Message<span class="text-danger">*</span></label>
							<textarea class="form-control" id="message" name="message" rows="10"></textarea>
							<div class="invalid-feedback">Message is required.</div>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-block btn-primary" id="send-message" name="send-message">Send Message</button>
					</div>
				</form>
			</div>
		</div>
	</body>	
</html>
<?php closeLogs(); ?>