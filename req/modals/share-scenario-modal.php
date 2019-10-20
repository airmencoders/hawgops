<div id="share-scenario-modal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center">Share Scenario <span id="share-scenario-name"></span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Enter the email address of who you want to share your scenario with.</p>
				<form method="POST" action="/do/share-scenario-do" onSubmit="return validateShareScenarioForm()">
					<div class="form-group">
						<label for="email-share">Email Address</label>
						<input type="hidden" id="scenario-name" name="scenario-name">
						<input type="hidden" id="share-scenario-id" name="share-scenario-id">
						<input type="email" class="form-control" id="email-share" name="email-share">
						<div class="invalid-feedback">Email Address is required.</div>
					</div>
					<button type="submit" class="btn btn-block btn-primary">Share Scenario</button>
				</form>
			</div>
		</div>
	</div>
</div>