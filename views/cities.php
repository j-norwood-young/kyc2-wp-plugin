<div class="wrap">
	<h1>KYC2 Ingester</h1>
	<hr>
	<form method="POST">
	<input type="hidden" name="action" value="select_cities">
	<?php submit_button("Save Settlements"); ?>
	<table class="widefat wp-list-table">
		<caption>Cities</caption>
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
				<th class="">Country</th>
				<th>City</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($data["form"] as $form_id=>$form) {
			foreach($form as $city_id=>$city) {
				if (!empty($city->{"section_A/A1_Country"})) {
		?>
			<tr>
				<th scope="row" class="check-column"><label class="screen-reader-text" for="">Select City</label><input type="checkbox" name="ona_cities[<?= $form_id ?>][]" value="<?= $city->_id ?>" id="checkbox_<?= $form_id ?>_<?= $city->_id ?>" <?= ((isset($data["ona_selected_cities"][$form_id]) && (in_array($city->_id, $data["ona_selected_cities"][$form_id])))) ? 'checked' :'' ?>></th>
				<td><?= $city->{"section_A/A1_Country"} ?></td>
				<td><?= $city->{"section_A/A2_City"} ?></td>
				<td><a href="/city/<?= $form_id ?>/<?= $city->_id ?>" target="_blank">Preview</a></td>
			</tr>
		<?php
				}
			}
		}
		?>
		</tbody>
	</table>
	<?php submit_button("Save Settlements"); ?>
	</form>
</div>
<pre>
	<?= print_r($data); ?>
</pre>