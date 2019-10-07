<div class="card" style="margin-top: 2rem;">
	<h2 class="card-header text-center">
		Airfield Weather Status
	</h2>
	<form>
		<div class="card-body">
			<div class="form-group">
				<label for="homestation">Homestation ICAO <span class="text-danger">* Required</span></label>
				<input type="text" id="homestation" name="homestation" class="form-control" max-length="4">
				<div class="form-control-feedback" id="homestation-fb"></div>
			</div>
			<div class="form-group">
				<label for="outbases">Out base ICAOs, separated by a space</label>
				<input type="text" id="outbases" name="outbases" class="form-control">
				<div class="form-control-feedback" id="outbases-fb"></div>
			</div>
		</div>
		<div class="card-footer">
			<button id="submit" class="btn btn-block btn-primary" type="submit">View Weather Data</button>
		</div>
	</form>				
</div>