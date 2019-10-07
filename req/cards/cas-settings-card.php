<div class="card" style="margin-top: 1rem;">
	<h4 class="card-header">Layer Settings</h4>
	<div class="card-body">
		<select id="basemap-select" class="custom-select bg-secondary text-white">
			<option value="ImageryClarity">Imagery Clarity</option>
			<option value="ImageryFirefly" selected>Imagery Firefly</option>
		</select>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="labels-switch" checked>
			<label class="custom-control-label" for="labels-switch">
				Map Labels
			</label>
		</div>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="roads-switch">
			<label class="custom-control-label" for="roads-switch">
				Road Labels
			</label>
		</div>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="mgrs-switch">
			<label class="custom-control-label" for="mgrs-switch">
				MGRS Gridlines
			</label>
		</div>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="airspace-switch">
			<label class="custom-control-label" for="airspace-switch">
				Airspace Borders
			</label>
		</div>							
	</div>
</div>