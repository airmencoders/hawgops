<div id="del-scenario-modal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center">Delete Scenario</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="load-scenario-body" class="modal-body">
				<p>Are you sure you want to delete your scenario <br/>"<span id="del-scenario-name"></span>"?</p>
			</div>
			<div class="modal-footer d-block">
				<form method="POST" action="/do/del-scenario-do">
					<input type="hidden" id="scenario-id" name="scenario-id">
					<button id="btn-del-scenario-confirm" type="submit" class="btn btn-block btn-danger"></button>
				</form>
			</div>
		</div>
	</div>
</div>