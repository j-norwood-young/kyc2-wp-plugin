<div class="wrap">
	<h1>KYC2 Ingester</h1>
	<hr>
	<form method="POST">
	<input type="hidden" name="action" value="select_settlements">
	<?php submit_button("Save Settlements"); ?>
	<table class="widefat wp-list-table">
		<caption>Settlements</caption>
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
				<th>Settlement Name</th>
				<th class="">Country</th>
				<th>City</th>
				<th>Municipality</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($data["form"] as $form_id=>$form) {
			foreach($form as $settlement_id=>$settlement) {
				if (!empty($settlement->{"section_B/B7_Settlement_Name_Community"})) {
		?>
			<tr>
				<th scope="row" class="check-column"><label class="screen-reader-text" for="">Select Settlement</label><input type="checkbox" name="ona_settlements[<?= $form_id ?>][]" value="<?= $settlement->_id ?>" id="checkbox_<?= $form_id ?>_<?= $settlement->_id ?>" <?= ((isset($data["ona_selected_settlements"][$form_id]) && (in_array($settlement->_id, $data["ona_selected_settlements"][$form_id])))) ? 'checked' :'' ?>></th>
				<td><?= $settlement->{"section_B/B7_Settlement_Name_Community"} ?></td>
				<td><?= $settlement->{"section_B/B3_Country"} ?></td>
				<td><?= $settlement->{"section_B/B5_City"} ?></td>
				<td><?= $settlement->{"section_B/B6_Municipality"} ?></td>
				<td><a href="/settlement/<?= $form_id ?>/<?= $settlement->_id ?>" target="_blank">Preview</a></td>
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