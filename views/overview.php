<div class="wrap">
	<h1>KYC2 Ingester</h1>
	<hr>
	
	<?php
	if ($data["test"]) {
	?>
	<p>
		<div class="dashicons dashicons-yes"></div> Connection to ONA successful.
	</p>
	<?php
	if (sizeof($data["forms"])) {
	?>
		<p>
			<div class="dashicons dashicons-yes"></div> You have selected <a href="<?= admin_url( 'admin.php?page=kyc2ingest-ona-forms' ); ?>"><?= sizeof($data["forms"]) ?> forms</a>.
		</p>
		<?php
		if (sizeof($data["settlements"])) {
		?>
			<p>
				<div class="dashicons dashicons-yes"></div> You have selected <a href="<?= admin_url( 'admin.php?page=kyc2ingest-ona-settlements' ); ?>"><?= sizeof($data["settlements"]) ?> settlements</a>.
			</p>
			
		<?php
		} else {
		?>
			<p>
				<div class="dashicons dashicons-no"></div> You haven't selected any settlements. Select which <a href="<?= admin_url( 'admin.php?page=kyc2ingest-ona-settlements' ); ?>">settlements</a> you would like to include.
			</p>
		<?php
		}
		?>
	<?php
	} else {
	?>
		<p>
			<div class="dashicons dashicons-no"></div> You haven't selected any forms. Select which <a href="<?= admin_url( 'admin.php?page=kyc2ingest-ona-forms' ); ?>">forms</a> you would like to include.
		</p>
	<?php
	}
	?>
	
	<?php
	} else {
	?>
	<p>
		<div class="dashicons dashicons-warning"></div> Could not connect to ONA. Please check your <a href="<?= admin_url( 'admin.php?page=kyc2ingest-ona-credentials' ); ?>">credentials</a>.
	</p>
	<?php
	}
	?>
	</p>

	<pre>
<?php print_r($data) ?>
	</pre>
</div>