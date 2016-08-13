<?php

// namespace KYC2Ingest;
// use KYC2Ingest;
include_once(plugin_basename("class.kyc2ingest-ona.php"));

class KYC2Ingest_Frontend {


	private static $initiated = false;
	private static $ona = false;

	public function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
		self::$ona = new KYC2Ingest\KYC2Ingest_ONA();
		self::$ona->init();
	}

	public function init_hooks() {
		add_filter('template_include', array("KYC2Ingest_Frontend", 'load_frontend_template'), 99 );
		add_filter("gettext", array("KYC2Ingest_Frontend", 'parse_definitions'), 99, 3);
	}

	public function load_frontend_template( $template ) {
		if (strpos($_SERVER["REQUEST_URI"], "/settlement") === 0) {
			wp_enqueue_style( 'leaflet', "https://npmcdn.com/leaflet@1.0.0-rc.3/dist/leaflet.css", array(), null );
			wp_enqueue_style("settlement_map", plugin_dir_url( __FILE__ ) . "css/settlement_map.css");
			wp_enqueue_script( 'leaflet', "https://npmcdn.com/leaflet@1.0.0-rc.3/dist/leaflet.js", array(), null );
			wp_enqueue_script("settlement_map", plugin_dir_url( __FILE__ ) . "js/settlement_map.js", array(), "", true);
			wp_enqueue_style("c3", plugin_dir_url( __FILE__ ) . "c3/c3.min.css");
			wp_enqueue_script("c3", plugin_dir_url( __FILE__ ) . "c3/c3.min.js");
			add_filter( 'get_settlement', array("KYC2Ingest_Frontend", 'return_settlement_data') );
			$new_template = locate_template( array( 'settlement.php' ) );
			if ( '' != $new_template ) {
				return $new_template ;
			}
		}
		return $template;
	}

	public function return_settlement_data() {
		if (strpos($_SERVER["REQUEST_URI"], "/settlement") === 0) {
			$s = trim($_SERVER["REQUEST_URI"], "/");
			$parts = explode("/", $s);
			if (sizeof($parts) < 3) {
				self::throw_404();
			}
			$settlement_data = self::$ona->data($parts[1] . "/" . $parts[2]);
			if ($settlement_data->detail === "Not found.") {
				self::throw_404();
			}
			$settlement_data->form_id = $parts[1];
			$shape = [];
			if ($settlement_data->{'verification/A0_Boundary'}) {
				$parts = explode(";", $settlement_data->{'verification/A0_Boundary'});
				foreach($parts as $part) {
					$ll = explode(" ", $part);
					$shape[] = [$ll[0], $ll[1]];
				}
			}
			$settlement_data->shape = $shape;
			global $title;
			$title = $settlement_data->{"section_B/B7_Settlement_Name_Community"} . ", " . $settlement_data->{"section_B/B5_City"} . ", " . $settlement_data->{"section_B/B3_Country"};
			add_filter('pre_get_document_title', function() {
				global $title;
				return $title;
			});
			global $kyc_definitions;
			$kyc_definitions = [];
			$defs = get_option("kyc2ingest_definitions");
			if (is_array($defs)) {
				foreach($defs as $def) {
					$kyc_definitions[$def["key"]] = $def["val"];
				}
			}
			global $wp_query;
			status_header( 200 );
			$wp_query->is_404=false;
			// add_filter('pre_get_document_title', function($title) { print "Testing - $title; "; return "test"; } );
			return $settlement_data;
		}
	}

	public function parse_definitions($translated_text, $text, $domain ) {
		global $kyc_definitions;
		if (!is_array($kyc_definitions)) {
			return $text;
		}
		$keys = array_keys($kyc_definitions);
		if (in_array($text, $keys)) {
			return $kyc_definitions[$text];
		}
		return $text;
	}

	protected static function throw_404() {
		status_header( 404 );
        nocache_headers();
        include( get_query_template( '404' ) );
        die();
	}
}