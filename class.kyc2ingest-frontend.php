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
	}

	public function load_frontend_template( $template ) {
		if (strpos($_SERVER["REQUEST_URI"], "/settlement") === 0) {
			wp_enqueue_style( 'leaflet', "https://npmcdn.com/leaflet@1.0.0-rc.3/dist/leaflet.css", array(), null );
			wp_enqueue_style("settlement_map", get_template_directory_uri() . "/settlement_map.css");
			wp_enqueue_script( 'leaflet', "https://npmcdn.com/leaflet@1.0.0-rc.3/dist/leaflet.js", array(), null );
			wp_enqueue_script("settlement_map", get_template_directory_uri() . "/settlement_map.js", array(), "", true);
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
			global $wp_query;
			status_header( 200 );
			$wp_query->is_404=false;
			// add_filter('pre_get_document_title', function($title) { print "Testing - $title; "; return "test"; } );
			return $settlement_data;
		}
	}

	protected static function throw_404() {
		status_header( 404 );
        nocache_headers();
        include( get_query_template( '404' ) );
        die();
	}
}