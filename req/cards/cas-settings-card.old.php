<div class="card" style="margin-top: 1rem;">
	<h4 class="card-header">Layer Settings</h4>
	<div class="card-body">
		<select id="basemap-select" class="custom-select bg-secondary text-white">
			<option value="Streets">Streets</option>
			<option value="Topographic">Topographic</option>
			<option value="NationalGeographic">National Geographic</option>
			<option value="Oceans">Oceans</option>
			<option value="Gray">Gray</option>
			<option value="DarkGray">Dark Gray</option>
			<option value="Imagery">Imagery</option>
			<option value="ImageryClarity">Imagery Clarity</option>
			<option value="ImageryFirefly" selected>Imagery Firefly</option>
			<option value="ShadedRelief">Shaded Relief</option>
			<option value="Terrain">Terrain</option>
			<option value="USATopo">USA Topographic</option>
			<option value="Physical">Physical</option>
		</select>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="labels-switch" checked>
			<label class="custom-control-label" for="labels-switch">
				Map Labels
			</label>
		</div>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="transportation-switch">
			<label class="custom-control-label" for="transportation-switch">
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