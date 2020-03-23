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
				<?php if(isset($_GET["scenario"])) { ?>
				<input type="hidden" id="scenario-id" name="scenario-id" value="<?php echo $_GET["scenario"]; ?>">
				<?php } ?>
				<?php if(isLoggedIn()) { ?>
				<div class="form-group">
					<label>Scenario Name</label>
					<input type="text" id="scenario-name" name="scenario-name" class="form-control" value="<?php echo $scenarioName; ?>">
				</div>
				<?php } ?>
				<div class="form-group">
					<label>Copy and Paste to Save</label>
					<input type="text" id="scenario-output" name="scenario-output" class="form-control">
				</div>
			</div>
			<div id="save-scenario-footer" class="modal-footer d-block">
				<?php if(isLoggedIn()) { ?>
				<button id="btn-save-to-account" class="btn btn-block btn-success"><?php echo(isset($_GET["scenario"]) && count($_GET["scenario"]) == 1) ? "Save new version to Account" : "Save to Account"; ?></button>
				<?php if(isset($_GET["scenario"]) && count($_GET["scenario"]) == 1) { ?>
				<button id="btn-update-scenario" class="btn btn-block btn-warning">Overwrite and Update Scenario</button>
				<?php } ?>
				<?php } ?>
				<button id="btn-copy-to-clipboard" class="btn btn-block btn-secondary">Copy To Clipboard</button>
			</div>
		</div>
	</div>
</div>