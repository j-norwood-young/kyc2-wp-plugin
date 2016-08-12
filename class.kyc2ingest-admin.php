<?php

// namespace KYC2Ingest;
// use KYC2Ingest;
include_once(plugin_basename("class.kyc2ingest-ona.php"));

class KYC2Ingest_Admin {


	private static $initiated = false;
	private static $ona = false;

	public function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
		self::$ona = new KYC2Ingest\KYC2Ingest_ONA();
		self::$ona->init();
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
		add_submenu_page( 'kyc2ingest', 'KYC ONA Forms', 'ONA Forms', 'manage_options', 'kyc2ingest-ona-forms', array("KYC2Ingest_Admin", "display_forms"));
		add_submenu_page( 'kyc2ingest', 'KYC ONA Settlements', 'Settlements', 'manage_options', 'kyc2ingest-ona-settlements', array("KYC2Ingest_Admin", "display_settlements"));
		// add_options_page("KYC2 Ingest Options", "KYC2 Ingest", "manage_options", "kyc2ingest-options", array("KYC2Ingest_Admin", "display_overview") );
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
		$data["ona_selected_forms"] = get_option("kyc2ingest_ona_forms");
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

	public static function display_overview() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$data = [];
		
		include(plugin_basename(  "/views/overview.php"));
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

	public function test_api() {
		if (!self::$ona->test()) {
			print "<div class='notice notice-warning is-dismissible'><p>Warning: Could not connect to ONA API. Please check username and password</p></div>";
		}
	}
}