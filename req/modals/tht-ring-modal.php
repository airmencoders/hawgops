<div id="tht-ring-modal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Create Threat Ring</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Mission Threat</label>
					<select id="msn-tht" class="custom-select">
						<option value="custom" selected>Custom</option>
						<option value="custom">--- RF SAMS ---</option>
						<option value="SA-2B/F">SA-2B/F</option>
						<option value="SA-2D/E">SA-2D/E</option>
						<option value="SA-3">SA-3</option>
						<option value="SA-5">SA-5</option>
						<option value="SA-6">SA-6</option>
						<option value="SA-8">SA-8</option>
						<option value="SA-10A/B">SA-10A/B</option>						
						<option value="SA-11">SA-11</option>
						<option value="SA-12A">SA-12A</option>
						<option value="SA-12B">SA-12B</option>
						<option value="SA-15">SA-15</option>
						<option value="SA-17">SA-17</option>
						<option value="SA-19">SA-19</option>
						<option value="SA-20">SA-20</option>
						<option value="SA-21">SA-21</option>
						<option value="SA-22">SA-22</option>
						<option value="custom">--- IR SAMS ---</option>
						<option value="SA-9">SA-9</option>
						<option value="SA-13">SA-13</option>	
					</select>
				</div>
				<div class="form-group">
					<label>Label</label>
					<input type="text" id="tht-ring-label" class="form-control">
				</div>
				<div class="form-group">
					<label>Radius</label>
					<div class="form-row">
						<div class="col-sm-10">
							<input type="text" id="tht-ring-radius" class="form-control" placeholder="3">
						</div>
						<div class="col-sm-2">
							<select id="msn-tht-units" class="custom-select">
								<option selected>NM</option>
								<option>m</option>
								<option>km</option>
							</select>
						</div>
					</div>
					<small id="tht-ring-radius-default-text" class="form-text text-center text-success font-weight-bolder">ALL VALUES ARE UNCLASSIFIED</small>
				</div>
				<div class="form-group">
					<label>Soverignty</label>
					<br/>
					<input type="text" id="tht-ring-color" class="form-control">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-create-tht-ring" class="btn btn-primary">Create Threat Ring</button>
			</div>
		</div>
	</div>
</div>