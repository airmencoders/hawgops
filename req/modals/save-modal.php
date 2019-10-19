<div id="save-modal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center">Save Scenario</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="save-scenario-body" class="modal-body">
				<?php if(isLoggedIn()) { ?>
				<div class="form-group">
					<label>Scenario Name</label>
					<input id="scenario-name" name="scenario-name" class="form-control">
				</div>
				<?php } ?>
				<div class="form-group">
					<label>Copy and Paste to Save</label>
					<input id="scenario-output" name="scenario-output" class="form-control">
				</div>
			</div>
			<div id="save-scenario-footer" class="modal-footer d-block">
				<?php if(isLoggedIn()) { ?>
				<button id="btn-save-to-account" class="btn btn-block btn-success">Save To Account</button>
				<?php } ?>
				<button id="btn-copy-to-clipboard" class="btn btn-block btn-secondary">Copy To Clipboard</button>
			</div>
		</div>
	</div>
</div>