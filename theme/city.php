<?php
	/*
	 * This is an example page for a Settlement that can be included in your theme. 
	 * The file must be called `city.php`
	 * When a user navigates to /city/<form_id>/<city_id>, 
	 * we will load this page, populating the data directly from ONA through the API.
	 *
	 * All the city data is stored in a variable called `$city`
	 *
	 * It includes maps, graphs, and translations so that you can turn unfriendly phrases like 
	 * "sanitation_sewage" into "Sanitation and Sewage".
	 * 
	 * To translate a field, use _e() to print or __() to return a value.
	 * 
	 */
	
	// We start with getting the city data. We also do some header mangling to avoid getting a 404, because according to Wordpress this page doesn't exist.
	$city = apply_filters( 'get_city', '' ); 
?>
<?php 
	// We get the headers, and also inject libraries for the maps and graphs
	get_header(); 
?>
<h1><a href="/city/<?= $city->form_id ?>/<?= $city->_id ?>"><?= $city->{"section_A/A2_City"} ?>, <?= $city->{"section_A/A1_Country"} ?></a></h1>

<h6>Last updated on <?= (isset($city->_last_edited)) ? $city->_last_edited : $city->_submission_time ?></h6>

<h2>History</h2>
<p><?= $city->{"section_A/A3_City_History"} ?></p>

<h2>Number of Profiled Settlements</h2>
<h3><?= $city->{"section_E/E2_Total_Profiled_Settlements"} ?></h3>

<h2>Which is % of total settlements in city</h2>
<div id="citywideGraph" class="graph" data-data='<?= json_encode(array("Settlements Profiled" => $city->{"section_B/B4_City_Wide_Achieved"})) ?>' data-charttype="gauge"></div>

<h2>Estimated informal settlement population</h2>
<h3><?= number_format($city->{"section_C/C2_Total_EstPopulation_Informal"}) ?></h3>

<h2>Settlement types</h2>
<h4>
	<ul>
		<li>Declared Settlements: <?= $city->{"section_D/D1_Total_Declared_Settlements"} ?></li>
		<li>Undeclared Settlements: <?= $city->{"section_D/D2_Total_Undeclared_Settlements"} ?></li>
		<li>Resettled Settlements: <?= $city->{"section_D/D3_Total_Resettled_Settlements"} ?></li>
	</ul>
</h4>

<h2>Settlements under eviction risk</h2>
<h3><?= number_format($city->{"section_D/D5_Eviction_Threat"}) ?>%</h3>

<h1>Development Priorities &amp; Community Organisation</h1>

<h2>Number of savings groups</h2>
<h4><?= $city->{"section_E/E1_Total_Savings_Groups"} ?></h4>

<h2>Top 3 priority development needs of settlements surveyed</h2>
<ul>
	<li><?= _e($city->{"section_B/development/B8_Priority1"}) ?></li>
	<li><?= _e($city->{"section_B/development/B9_Priority2"}) ?></li>
	<li><?= _e($city->{"section_B/development/B10_Priority3"}) ?></li>
</ul>

<h2>Number of Settlement Forums</h2>
<h4><?= $city->{"section_E/E2a_Num_SettForums"} ?></h4>

<h2>City Forums &amp; Meeting Frequency</h2>
<h3>Forums</h3>
<h4><?= _e($city->{"section_E/E3_City_Forums"}) ?></h4>
<h3>Frequency</h3>
<h4><?= _e($city->{"section_E/E3a_Frequency"}) ?></h4>
<pre>
<?php
	/* This is to help with building - it's a quick way to look up the parameters available for display.
	 * 
	 * Remember to remove it for production!
	 *
	 */
	print_r($city) 
?>
</pre>
<?php get_footer(); ?>