<?php
	$level = "";
	$text = "";

	// s = status
	if(isset($_GET["s"]) && $_GET["s"] != "") {
		if($_GET["s"] > 6000) {
			$level = "info";
		} else if($_GET["s"] > 5000) {
			$level = "warning";
		} else if($_GET["s"] > 4000) {
			$level = "danger";
		} else if($_GET["s"] > 3000) {
			$level = "success";
		} else if($_GET["s"] > 2000) {
			$level = "secondary";
		} else if($_GET["s"] > 1000) {
			$level = "primary";
		}
	} 

	switch($_GET["s"]) {
		case 3101:
			$text = "Message sent to Porkins.";
			break;
		case 4101:
			$text = "Message data not received.";
			break;
		case 4102:
			$text = "Name not received.";
			break;
		case 4103:
			$text = "Email address not received.";
			break;
		case 4104:
			$text = "Email message not received.";
			break;
		case 4105:
			$text = "Unknown Error: Message not sent.";
			break;
	}

	if($text != "") {
?>
	<div class="alert alert-<?php echo $level; ?> alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<i class="fa fa-times"></i>
		</button>
		<?php echo $text; ?>
	</div>
<?php
	}
?>