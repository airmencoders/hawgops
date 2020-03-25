<?php
	require("../req/all/api-v2.php");
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php require("../req/head/head.php"); ?>
	</head>
	<body id="bg">				
		<div id="body-container" class="container">
		
			<div class="card text-center mx-auto" style="max-width: 35rem; margin-top: 2rem;">
				<div class="card-body">
					<h5 class="card-title"><i class="fas fa-tools fa-5x"></i></h5>
					<p class="card-text">UUID: <?php echo createUUID(); ?></a>
				</div>
			</div>
		</div>
	</body>
</html>