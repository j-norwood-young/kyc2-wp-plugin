<div class="wrap">
	<h1>KYC2 Ingester</h1>
	<hr>
	<form method="POST">
	<input type="hidden" name="action" value="select_forms">
	<h2>Forms</h2>
	<p>Select the forms you would like to pull settlement data from.</p>
	<ul>
	<?php
	foreach($data["ona_forms"] as $form) {
	?>
		<li> <input type="checkbox" name="ona_forms[]" value="<?= $form->id ?>" <?php
		if (in_array($form->id, $data["ona_selected_forms"])) {
		?>
		checked="checked"
		<?php
		}
		?>> <a href="/demo.php?id=<?= $form->id ?>"><?= $form->title ?> (<?= $form->id ?>)</a></li>
	<?php
	}
	?>
	</ul>
	<?php submit_button("Save Selected Forms"); ?>
	</form>
</div>