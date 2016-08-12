<div class="wrap">
	<h1>KYC2 Ingester</h1>
	<hr>
	<h2>ONA Credentials</h2>
	<form method="POST">
		<input type="hidden" name="action" value="ona_credentials">
		<table class="form-table">
			<tr>
				<th><label for="ona_username">ONA username</label></th>
				<td><input type="text" name="ona_username" value="<?= $data["ona_credentials"]["username"] ?>"></td>
			</tr>
			<tr>
				<th><label for="ona_password">ONA password</label></th>
				<td><input type="password" name="ona_password"></td>
			</tr>
		</table>
		<?php submit_button("Save ONA Credentials"); ?>
	</form>
</div>