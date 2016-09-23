<?php

// namespace KYC2Ingest;
// use KYC2Ingest;
include_once(plugin_basename("class.kyc2ingest-ona.php"));
include_once(plugin_basename("class.kyc2ingest-cache.php"));

class KYC2Ingest_Admin {


	private static $initiated = false;
	private static $ona = false;
	private static $cache = false;

	public function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
		self::$ona = new KYC2Ingest\KYC2Ingest_ONA();
		self::$ona->init();
		self::$cache = new KYC2Ingest\KYC2Ingest_Cache();
		self::$cache->init();
		if (isset($_POST["action"])) {
			self::process_form($_POST["action"]);
		}
	}

	public function init_hooks() {
		self::$initiated = true;
		add_action( 'admin_menu', array( 'KYC2Ingest_Admin', 'admin_menu' ));
		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'kyc2ingest.php'), array( 'KYC2Ingest_Admin', 'admin_plugin_settings_link' ) );
		add_action( 'admin_notices', array( 'KYC2Ingest_Admin', 'test_api' ) );
	}

	public function admin_menu() {
		add_menu_page('KYC2 Ingester', 'KYC', 'manage_options', 'kyc2ingest', array("KYC2Ingest_Admin", "display_overview"));
		add_submenu_page( 'kyc2ingest', 'KYC Credentials', 'Credentials', 'manage_options', 'kyc2ingest-ona-credentials', array("KYC2Ingest_Admin", "display_credentials"));
		add_submenu_page( 'kyc2ingest', 'KYC Forms', 'Forms', 'manage_options', 'kyc2ingest-ona-forms', array("KYC2Ingest_Admin", "display_forms"));
		add_submenu_page( 'kyc2ingest', 'KYC Settlements', 'Settlements', 'manage_options', 'kyc2ingest-ona-settlements', array("KYC2Ingest_Admin", "display_settlements"));
		add_submenu_page( 'kyc2ingest', 'KYC Cities', 'Cities', 'manage_options', 'kyc2ingest-ona-cities', array("KYC2Ingest_Admin", "display_cities"));
		add_submenu_page( 'kyc2ingest', 'KYC Translations', 'Translations', 'manage_options', 'kyc2ingest-definitions', array("KYC2Ingest_Admin", "display_definitions"));
		add_submenu_page( 'kyc2ingest', 'Refresh Cache', 'Refresh Cache', 'manage_options', 'kyc2ingest-cache-test', array("KYC2Ingest_Admin", "cache_test"));
	}

	public static function admin_plugin_settings_link( $links ) { 
  		$settings_link = '<a href="/wp-admin/options-general.php?page=kyc2ingest-options">Settings</a>';
  		array_unshift( $links, $settings_link );
  		return $links; 
	}

	public static function display_credentials() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		$data["ona_credentials"] = get_option("kyc2ingest_ona_credentials");
		include(plugin_basename(  "/views/credentials.php"));
	}

	public static function display_forms() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		$data["ona_forms"] = self::$ona->data();
		$data["ona_selected_forms"] = self::get_selected_forms();
		include(plugin_basename(  "/views/forms.php"));
	}

	public static function display_settlements() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		$forms = get_option("kyc2ingest_ona_forms");
		$data["ona_selected_settlements"] = get_option("kyc2ingest_settlements");
		foreach($forms as $form_id) {
			$data["form"][$form_id] = self::$ona->data($form_id);
		}
		include(plugin_basename(  "/views/settlements.php"));
	}

	public static function display_cities() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		$forms = get_option("kyc2ingest_ona_forms");
		$data["ona_selected_cities"] = get_option("kyc2ingest_cities");
		foreach($forms as $form_id) {
			$data["form"][$form_id] = self::$ona->data($form_id);
		}
		include(plugin_basename(  "/views/cities.php"));
	}

	public static function display_overview() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		$data["test"] = self::$ona->test();
		$data["forms"] = self::get_selected_forms();
		$data["settlements"] = self::flatten_array(get_option("kyc2ingest_settlements"));
		include(plugin_basename(  "/views/overview.php"));
	}

	public static function display_definitions() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		$data["definitions"] = get_option("kyc2ingest_definitions");
		include(plugin_basename(  "/views/definitions.php"));
	}

	public function process_form($action) {
		switch ($action) {
			case "ona_credentials":
				self::save_ona_credentials();
				break;
			case "select_forms":
				self::save_select_forms();
				break;
			case "select_settlements":
				self::save_select_settlements();
				break;
			case "select_cities":
				self::save_select_cities();
				break;
			case "save_definitions":
				self::save_definitions();
				break;
		}
	}

	public function save_ona_credentials() {
		$data = [
			"username" => $_POST["ona_username"],
			"password" => $_POST["ona_password"]
		];
		update_option("kyc2ingest_ona_credentials", $data);
		return true;
	}

	public function save_select_forms() {
		update_option("kyc2ingest_ona_forms", $_POST["ona_forms"]);
		return true;
	}

	public function save_select_settlements() {
		update_option("kyc2ingest_settlements", $_POST["ona_settlements"]);
		return true;
	}

	public function save_select_cities() {
		update_option("kyc2ingest_cities", $_POST["ona_cities"]);
		return true;
	}

	public function save_definitions() {
		$data = [];
		foreach($_POST["definitions"] as $key=>$val) {
			if (!empty($val["key"])) {
				$data[] = $val;
			}
		}
		update_option("kyc2ingest_definitions", $data);
		return true;
	}

	public function test_api() {
		if (!self::$ona->test()) {
			print "<div class='notice notice-warning is-dismissible'><p><div class='dashicons dashicons-warning'></div> Could not connect to ONA. Please check your <a href='" . admin_url( 'admin.php?page=kyc2ingest-ona-credentials' ) . "'>credentials</a>.</p></div>";
		}
	}

	public function cache_test() {
		self::$cache->cache_cities();
		self::$cache->cache_settlements();
	}

	private static function flatten_array($arr) {
		if ((!is_array($arr)) || (sizeof($arr) === 0)) {
			return [];
		}
		$return = [];
		array_walk_recursive($arr, function($a) use (&$return) { $return[] = $a; });
		return $return;
	}

	private static function get_selected_forms() {
		$forms = get_option("kyc2ingest_ona_forms");
		if (!is_array($forms)) {
			$forms = [];
		}
		return $forms;
	}
}