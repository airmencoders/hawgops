<div id="edit-cap-modal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit CAP</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Label</label>
					<input type="text" id="edit-cap-label" class="form-control">
				</div>
				<div class="form-group">
					<label>Length</label>
					<div class="input-group">
						<input type="text" id="edit-cap-length" class="form-control" placeholder="10">
						<div class="input-group-append">
							<span class="input-group-text">NM</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label>Color</label>
					<br/>
					<input type="text" id="edit-cap-color" class="form-control">
				</div>
				<div class="form-group">
					<label>Angle</label>
					<input type="number" id="edit-cap-angle" class="form-control" min="-90" max="90" placeholder="0">
					<small class="form-text text-muted">-90(W) to 90(E)</small>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-save-cap" class="btn btn-primary">Save Changes</button>
			</div>
		</div>
	</div>
</div>