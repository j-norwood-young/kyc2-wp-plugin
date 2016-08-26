<?php
	/*
	 * This is an example page for a Settlement that can be included in your theme. 
	 * The file must be called `settlement.php`
	 * When a user navigates to /settlement/<form_id>/<settlement_id>, 
	 * we will load this page, populating the data directly from ONA through the API.
	 *
	 * All the settlement data is stored in a variable called `$settlement`
	 *
	 * It includes maps, graphs, and translations so that you can turn unfriendly phrases like 
	 * "sanitation_sewage" into "Sanitation and Sewage".
	 * 
	 * To translate a field, use _e() to print or __() to return a value.
	 * 
	 */
	
	// We start with getting the settlement data. We also do some header mangling to avoid getting a 404, because according to Wordpress this page doesn't exist.
	$settlement = apply_filters( 'get_settlement', '' ); 
?>
<?php 
	// We get the headers, and also inject libraries for the maps and graphs
	get_header(); 
?>

<!-- The map -->
<div id="mapid"></div>

<?php 
	/* You can see below how the URL is formed: /settlement/<?= $settlement->form_id ?>/<?= $settlement->_id ?> 
	 *
	 * Note how we address fields as strings, because of the /
	 *
	 */
?>

<h1><a href="/settlement/<?= $settlement->form_id ?>/<?= $settlement->_id ?>"><?= $settlement->{"section_B/B7_Settlement_Name_Community"} ?>, <?= $settlement->{"section_B/B5_City"} ?>, <?= $settlement->{"section_B/B3_Country"} ?></a></h1>
	<h6>Last updated on <?= (isset($settlement->{"section_A/A1a_Last_Updated"})) ? $settlement->{"section_A/A1a_Last_Updated"} : $settlement->{"section_A/A1_Profile_Date"} ?></h6>
	<h2>History</h2>
	<p>
		<strong>Established:</strong> <?= $settlement->{"section_B/B10b_Year_Established"} ?>
	</p>
	<p>
		<?= $settlement->{"section_B/B12_History"} ?>
	</p>

	<?php
		/*
		 * We can use PHP to cheat a bit, running through the priorities, just watch out for empty "others"
		 */
	?>
	<h2>Priority Development Needs</h2>
	<ul>
		<?php
			for($x = 1; $x <= 5; $x++) {
				$priority_name = "section_Q/Q{$x}_Priority{$x}";
				$priority_comment = "section_Q/Q{$x}b_Priority{$x}_Comments";
				if (!empty($settlement->{$priority_name})) {
					if ($settlement->{$priority_name} === "other" && (!empty($settlement->{$priority_comment}))) {
				?>
					<li>
						<?= $settlement->{$priority_comment} ?>
					</li>
				<?php
					} elseif($settlement->{$priority_name} !== "other") {  ?>
					<li>
						<?= _e($settlement->{$priority_name}) ?>
					</li>
				<?php	}
				}
			}
		?>
	</ul>
	
	<h2>Eviction Threat</h2>
	<h3><?= ucfirst($settlement->{"section_E/E2B_Current_Eviction_Seriousness"}) ?></h3>
	<p>Status: <strong><?= _e($settlement->{"section_B/B14_Status"}) ?></strong></p>

	<h2>Land Ownership</h2>
	<?php
		/*
		 * You can see how we build a graph here. We just put all the data 
		 * into an associated array, and drop it in to an element with 
		 * the data json_encoded in the attribute `data-data`.
		 * 
		 * Make sure the class is `graph` and that you give it a unique ID.
		 * Once you've done that, it will work like magic.
		 *
		 */
	?>
	<p>
		<?php
			$data = [
				"Private owner" => $settlement->{"section_B/B13_Ownership_private_owner"}, 
				"Airport authority" => $settlement->{"section_B/B13_Ownership_airport_authority"}, 
				"Church land" => $settlement->{"section_B/B13_Ownership_church_land"}, 
				"Port trust" => $settlement->{"section_B/B13_Ownership_port_trust"}, 
				"Other" => $settlement->{"section_B/B13_Ownership_other_percentage"}, 
				"Customary land" => $settlement->{"section_B/B13_Ownership_customary_land"}, 
				"Other government" => $settlement->{"section_B/B13_Ownership_other_government_percentage"}, 
				"Municipality" => $settlement->{"section_B/B13_Ownership_municipality"}, 
				"Crown land" => $settlement->{"section_B/B13_Ownership_crown_land"}, 
				"Defense" => $settlement->{"section_B/B13_Ownership_defense"}, 
				"Railway" => $settlement->{"section_B/B13_Ownership_railway"}, 
				"Unknown" => $settlement->{"section_B/B13_Ownership_Unknown"}
			];
		?>
		<div id="landOwnershipGraph" class="graph" data-data='<?= json_encode($data) ?>'></div>
	</p>

	<h2>Estimated number of structures</h2>
	<p><?= number_format($settlement->{"section_C/C5_Structures_Total"}) ?></p>

	<h2>Estimated population</h2>
	<p><?= number_format($settlement->{"section_C/C12_Total_Population"}) ?></p>

	<h2>Area/Size</h2>
	<p><?= number_format($settlement->{"section_B/B2b_Area_acres"}) ?> Acres</p>

	<h2>Population density</h2>
	<p><?= number_format($settlement->{"section_C/C12_Total_Population"} / $settlement->{"section_B/B2b_Area_acres"}) ?> per Acre / <?= number_format($settlement->{"section_C/C12_Total_Population"}) ?></p>

	<h1>Water and Sanitation</h1>

	<h2>Number of shared and community taps</h2>
	<?php
		$data = [
			"Taps Not Working" => $settlement->{"section_F/F1_Count"} - $settlement->{"section_F/F1_Working"},
			"Taps Working" => $settlement->{"section_F/F1_Working"},
		];
	?>
	<div id="tapsGraph" class="graph" data-data='<?= json_encode($data) ?>'></div>

	<h2>Number of working taps</h2>
	<p><?= $settlement->{"section_F/F1_Working"} ?></p>

	<h2>Ratio of working taps to people</h2>
	<p>1 : <?= number_format($settlement->{"section_C/C12_Total_Population"} / $settlement->{"section_F/F1_Working"}) ?></p>

	<h2>Average cost of access per month</h2>
	<p><?= number_format($settlement->{"section_F/F11_Water_MonthlyCost"}) ?></p>

	<h2>Number of communal toilets shared</h2>
	<?php
		// Here you can see us using __ instead of _e, because we want to return the value rather than printing it
		$data = [
			__($settlement->{"section_G/G7_Toilet_Type"}) . " Working" => $settlement->{"section_G/G7_Working"},
			__($settlement->{"section_G/G8_Toilet_Type"}) . " Working" => $settlement->{"section_G/G8_Working"},
			__($settlement->{"section_G/G9_Toilet_Type"}) . " Working" => $settlement->{"section_G/G9_Working"},
			__($settlement->{"section_G/G10_Toilet_Type"}) . " Working" => $settlement->{"section_G/G10_Working"},
		];

		$toilet_count = $settlement->{"section_G/G7_Working"} + $settlement->{"section_G/G8_Working"} + $settlement->{"section_G/G9_Working"} + $settlement->{"section_G/G10_Working"}
	?>
	<div id="toiletsGraph" class="graph" data-data='<?= json_encode($data) ?>'></div>

	<h2>Number of working toilets</h2>
	<p><?= number_format($toilet_count) ?></p>

	<h2>Ratio of working toilets to people</h2>
	<p>1 : <?= number_format($settlement->{"section_C/C12_Total_Population"} / $toilet_count) ?></p>

	<h1>Infrastructure</h1>
	
	<h2>Electricity Available</h2>
	<p><?= ucfirst($settlement->{"section_J/J1_Electricity_Available"}) ?> (<?= $settlement->{"section_J/J5_Electricity_HoursPerDay"} ?> per day)</p>

	<h2>Garbage collections</h2>
	<p><?= _e($settlement->{"section_H/H6_Garbage_WeeklyCollections"}) ?> per week</p>

	<h2>Garbage location</h2>
	<p><?= _e($settlement->{"section_H/H1_Garbage_Location"}) ?></p>

	<h2>How do people in the settlement access their homes?</h2>
	<p><?= _e($settlement->{"section_L/L5_Road_Type"}) ?></p>

	<h2>Main means of transportation</h2>
	<p><?= $settlement->{"section_L/L1_Transport_Modes"} ?></p>
<pre>
<?php
	/* This is to help with building - it's a quick way to look up the parameters available for display.
	 * 
	 * Remember to remove it for production!
	 *
	 */
	print_r($settlement) 
?>
</pre>
<?php
	/*
	 * Here we inject our mapping data. This might move to another page at some point.
	 */
?>
<script type="text/javascript" charset="utf-8">
	var midLat = "<?= $settlement->_geolocation[0] ?>";
	var midLng = "<?= $settlement->_geolocation[1] ?>";
	var shape = <?= json_encode($settlement->shape) ?>; // jshint ignore:line
</script>
<?php get_footer(); ?>
