<?php
namespace KYC2Ingest;

include_once(plugin_basename("class.kyc2ingest-ona.php"));
global $kyc2_db_version;
$kyc2_db_version = '1.8';

class KYC2Ingest_Cache {

	private static $ona = false;
	private static $table_name;
	private static $wpdb;

	public function init() {
		global $wpdb;
		self::$wpdb = &$wpdb;
		self::$ona = new KYC2Ingest_ONA();
		self::$ona->init();
		self::$table_name = $wpdb->prefix . "kyc2ingest";
		self::db_install();
	}

	public function cache_settlements() {
		$selected_settlements = get_option("kyc2ingest_settlements");
		foreach($selected_settlements as $selected_settlement) {
			// print_r($selected_settlement);
		}
		// $tmp = get_option("kyc2ingest_ona_forms");
		$selected_settlements = get_option("kyc2ingest_settlements");
		// print "<pre>";
		// print_r($settlements);
		// print "</pre>";
		// return;
		print "<h3>Settlement Cache refreshed</h3>";
		foreach($selected_settlements as $form_id=>$settlements) {
			$form_data = self::$ona->data($form_id);
			foreach($form_data as $settlement) {
				if (in_array($settlement->_id, $settlements)) {
					$data = [ 
						"ona_id" => $settlement->_id,
						"form_id" => $form_id,
						"name" => $settlement->{"section_B/B7_Settlement_Name_Community"},
						"city_id" => $settlement->{"section_B/B5_City"}, 
						"country_id" => $settlement->{"section_B/B3_Country"}, 
						"gps" => $settlement->{"section_B/B1_GPS"} 
					];
					self::$wpdb->replace(self::$table_name . "_settlements", $data);
				}
				print $form_id . "/" . $settlement->_id . ": " . $settlement->{"section_B/B7_Settlement_Name_Community"} . " (" . $settlement->{"section_B/B5_City"} . ", " . $settlement->{"section_B/B3_Country"} . ")<br>\n";
			}
		}
	}

	public function cache_cities() {
		$selected_cities = get_option("kyc2ingest_cities");
		print "<h3>City Cache refreshed</h3>";
		foreach($selected_cities as $form_id=>$cities) {
			$form_data = self::$ona->data($form_id);
			foreach($form_data as $city) {
				if (in_array($city->_id, $cities)) {
					$data = [ 
						"ona_id" => $city->_id,
						"form_id" => $form_id,
						// "name" => $city->{"section_A/A2_City"}, 
						"city_id" => $city->{"section_A/A2_City"}, 
						"country_id" => $city->{"section_A/A1_Country"}, 
						"gps" => $city->{"section_B/B1_GPS"} 
					];
					
					self::$wpdb->replace(self::$table_name . "_cities", $data);
				}
				print $form_id . "/" . $city->_id . ": " . $city->{"section_A/A2_City"} . " (" . $city->{"section_A/A1_Country"} . ")<br>\n";
			}
		}
		if (self::$wpdb->last_error) {
			print "<strong>Sql Error</strong>" . self::$wpdb->last_error;
		}
	}

	function db_install () {
		global $kyc2_db_version;
		$installed_ver = get_option( "kyc2_db_version" );
		if ( $installed_ver != $kyc2_db_version ) {
			$charset_collate = self::$wpdb->get_charset_collate();
			$sql = "CREATE TABLE " . self::$table_name . "_settlements ( 
				`id` INT NOT NULL AUTO_INCREMENT , 
				`ona_id` INT NOT NULL , 
				`form_id` INT NOT NULL , 
				`city_id` VARCHAR(100) NOT NULL , 
				`name` VARCHAR(100) NOT NULL , 
				`country_id` VARCHAR(100) NOT NULL ,  
				`gps` VARCHAR(200) NOT NULL , 
				`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
				`last_cache` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
				PRIMARY KEY  (`id`), 
				INDEX (`city_id`), 
				INDEX (`country_id`), 
				INDEX (`last_cache`), 
				UNIQUE (`ona_id`)
				) $charset_collate;";
			$sql .= "CREATE TABLE " . self::$table_name . "_cities ( 
				`id` INT NOT NULL AUTO_INCREMENT , 
				`ona_id` INT NOT NULL , 
				`form_id` INT NOT NULL , 
				`city_id` VARCHAR(100) NOT NULL , 
				`country_id` VARCHAR(100) NOT NULL ,  
				`gps` VARCHAR(200) NOT NULL , 
				`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
				`last_cache` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
				PRIMARY KEY  (`id`), 
				INDEX (`country_id`), 
				INDEX (`last_cache`), 
				UNIQUE (`ona_id`)
				) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
			update_option( 'kyc2_db_version', $kyc2_db_version );
		}
	}
}