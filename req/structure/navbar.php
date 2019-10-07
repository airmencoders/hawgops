<div id="class-banner" class="bg-success text-center">
	<div class="text-center font-weight-bolder">
		// UNCLASSIFIED //
	</div>
</div>
<nav class="navbar navbar-expand-xl navbar-dark bg-dark fixed-top">
	<a class="navbar-brand" href="/">Hawg Ops</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#odtNavbar" aria-controls="odtNavbar" aria-expanded="false" aria-label="Toggle Navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse justify-content-between" id="odtNavbar">
		<ul class="navbar-nav">
			<!--<li class="nav-item">
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/wx.php") ? "active" : ""; ?>" href="/wx">WX</a>
			</li>-->
			<li class="nav-item">	
				<a class="nav-link <?php echo ($_SERVER["PHP_SELF"] == "/cas.php") ? "active" : ""; ?>" href="/cas">CAS Planner</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://badbirdbook.com" target="_blank">A-10 Bad Bird Book <i class="fas fa-external-link-alt"></i></a>
			</li>
		</ul>
		<ul class="navbar-nav">
			<li class="nav-item">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" class="form-inline">
					<input type="hidden" name="cmd" value="_donations" />
					<input type="hidden" name="business" value="DW3ED6VK2DGKW" />
					<input type="hidden" name="currency_code" value="USD" />
					<button type="submit" name="submit" class="btn btn-sm btn-dark btn-donate nav-link"><i class="fab fa-2x fa-cc-paypal"></i></button>
				</form>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/contact">Contact Porkins</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="policy-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Policies
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="policy-toggle">
					<a class="dropdown-item <?php echo ($_SERVER["PHP_SELF"] == "/licenses.php") ? "active" : ""; ?>" href="./licenses">Licenses</a>
				</div>
			</li>
		</ul>
	</div>
</nav>