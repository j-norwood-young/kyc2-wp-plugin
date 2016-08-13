<div class="wrap">
	<h1>Translations</h1>
	<hr>
	<p>Use this form to translate symbols in your data (eg. dirt_paths) into what you want to display (eg. Dirt Paths)</p>
	<p>Developer note: To mark a variable for translation in your theme, simply wrap in in the <code>_e()</code> function.</p>
<form method="POST">
	<input type="hidden" name="action" value="save_definitions">
	<table>
		<thead>
			<tr>
				<th>Symbol</th>
				<th>Translation</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="text" name="definitions[-1][key]"></td>
				<td><input type="text" name="definitions[-1][val]"></td>
				<td></td>
			</tr>
			<?php
			foreach($data["definitions"] as $key=>$definition) {
			?>
			<tr>
				<td><input type="text" name="definitions[<?= $key ?>][key]" value="<?= $definition["key"] ?>"></td>
				<td><input type="text" name="definitions[<?= $key ?>][val]" value="<?= $definition["val"] ?>"></td>
				<td><div class="del-row dashicons dashicons-no-alt"></div></td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
	<?php submit_button("Save Definitions"); ?>
</form>
</div>
<script>
	jQuery(function() {
		jQuery(".del-row").on("click", function() {
			jQuery(this).parents("tr").remove();
		});
	});
</script>