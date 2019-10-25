<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Close Air Support Planner and other tools to assist aircrew with mission planning">

<!-- Vendor CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<!-- Custom CSS -->
<link href="/css/hawg-ops.css" rel="stylesheet">

<!-- Vendor JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/77e4a7824a.js" crossorigin="anonymous"></script>

<link rel="icon" href="https://hawg-ops.com/images/favicon.png">
<title>Hawg Ops</title>
<script>
	$(document).ready(function() {
		window.addEventListener("online", function() {
			<!-- "Online" returns true even if connected to LAN but don't have actual internet, so test to see if you have internet -->
			<?php /*
			$.ajax({
				url: "https://hawg-ops.com/images/favicon.png",
				crossDomain: true,
				error: function(xhr, ajaxOptions, thrownError) {
					$("#notify-online").addClass("d-none");
					$("#notify-offline").removeClass("d-none");
				},
				success: function(data, textStatus, jqXHR) {
					$("#notify-online").removeClass("d-none");
					$("#notify-offline").addClass("d-none");
				}
			});*/
			?>
			$("#notify-online").removeClass("d-none");
			$("#notify-offline").addClass("d-none");
			
		});
		
		window.addEventListener("offline", function() {
			$("#notify-online").addClass("d-none");
			$("#notify-offline").removeClass("d-none");
		});
	});
</script>