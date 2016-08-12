<?php $settlement = apply_filters( 'get_settlement', '' ); ?>
<?php get_header(); ?>
<?php
$dev_translation = ["water_drainage" => "Water Drainage", "sanitation_sewage" => "Sanitation and Sewerage"];
?>

<div id="mapid"></div>

<h1><a href="/settlement/<?= $settlement->form_id ?>/<?= $settlement->_id ?>"><?= $settlement->{"section_B/B7_Settlement_Name_Community"} ?>, <?= $settlement->{"section_B/B5_City"} ?>, <?= $settlement->{"section_B/B3_Country"} ?></a></h1>
	<h6>Last updated on <?= (isset($settlement->{"section_A/A1a_Last_Updated"})) ? $settlement->{"section_A/A1a_Last_Updated"} : $settlement->{"section_A/A1_Profile_Date"} ?></h6>
	<h2>History</h2>
	<p>
		<strong>Established:</strong> <?= $settlement->{"section_B/B10b_Year_Established"} ?>
	</p>
	<p>
		<?= $settlement->{"section_B/B12_History"} ?>
	</p>

	<h2>Priority Development Needs</h2>
	<ul>
		<?php
			for($x = 1; $x <= 5; $x++) {
				$priority_name = "section_Q/Q{$x}_Priority{$x}";
				$priority_comment = "section_Q/Q{$x}b_Priority{$x}_Comments";
				if (isset($settlement->{$priority_name})) {
				?>
					<li>
						<?= ($settlement->{$priority_name} === "other") ? $settlement->{$priority_comment} : $dev_translation[$settlement->{$priority_name}] ?>
					</li>
				<?php
				}
			}
		?>
	</ul>
	
	<h2>Eviction Threat</h2>
	<h3><?= ucfirst($settlement->{"section_E/E2B_Current_Eviction_Seriousness"}) ?></h3>
	<p>Status: <strong><?= $settlement->{"section_B/B14_Status"} ?></strong></p>

	<h2>Land Ownership</h2>
	<p>Graph here</p>

	<h2>Estimated number of structures</h2>
	<p><?= number_format($settlement->{"section_C/C5_Structures_Total"}) ?></p>

	<h2>Estimated population</h2>
	<p><?= number_format($settlement->{"section_C/C12_Total_Population"}) ?></p>

	<h2>Area/Size</h2>
	<p><?= number_format($settlement->{"section_B/B2b_Area_acres"}) ?> Acres / <?= number_format($settlement->{"section_B/B2a_Area_sqmt"}) ?> m<sup>2</sup></p>

	<h2>Population density</h2>
	<p><?= number_format($settlement->{"section_C/C12_Total_Population"} / $settlement->{"section_B/B2b_Area_acres"}) ?> per Acre / <?= number_format($settlement->{"section_C/C12_Total_Population"} / $settlement->{"section_B/B2a_Area_sqmt"} * 1000) ?> per km<sup>2</sup></p>

	<h1>Water and Sanitation</h1>

	<h2>Number of shared and community taps</h2>
	<p>Graph here</p>

	<h2>Number of working taps</h2>
	<p><?= $settlement->{"section_F/F3_Working"} ?></p>

	<h2>Ratio of working taps to people</h2>
	<p>1 : <?= number_format($settlement->{"section_C/C12_Total_Population"} / $settlement->{"section_F/F3_Working"}) ?></p>

	<h2>Average cost of access per month</h2>
	<p><?= number_format($settlement->{"section_F/F11_Water_MonthlyCost"}) ?></p>

	<h2>Number of communal toilets shared</h2>
	<p>???</p>

	<h2>Number of working toilets</h2>
	<p><?= number_format($settlement->{"section_G/G7_Working"}) ?></p>

	<h2>Ratio of working toilets to people</h2>
	<p>1 : <?= number_format($settlement->{"section_C/C12_Total_Population"} / $settlement->{"section_G/G7_Working"}) ?></p>

	<h1>Infrastructure</h1>
	
	<h2>Electricity Available</h2>
	<p><?= ucfirst($settlement->{"section_J/J1_Electricity_Available"}) ?> (<?= $settlement->{"section_J/J5_Electricity_HoursPerDay"} ?> per day)</p>

	<h2>Garbage collections</h2>
	<p><?= $settlement->{"section_H/H6_Garbage_WeeklyCollections"} ?> per week</p>

	<h2>Garbage location</h2>
	<p><?= $settlement->{"section_H/H1_Garbage_Location"} ?></p>

	<h2>How do people in the settlement access their homes?</h2>
	<p><?= $settlement->{"section_L/L5_Road_Type"} ?></p>

	<h2>Main means of transportation</h2>
	<p><?= $settlement->{"section_L/L1_Transport_Modes"} ?></p>
<pre>
<?php print_r($settlement) ?>
</pre>
<script type="text/javascript" charset="utf-8">
	var midLat = "<?= $settlement->_geolocation[0] ?>";
	var midLng = "<?= $settlement->_geolocation[1] ?>";
	var shape = <?= json_encode($settlement->shape) ?>; // jshint ignore:line
	
</script>
<?php get_footer(); ?>
